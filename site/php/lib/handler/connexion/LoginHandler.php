<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 15:17
 */

namespace handler\connexion;


use handler\DefaultRanAndSucceed;
use handler\GenericPDORequestHandler;
use handler\HandlerVisitor;
use model\LoginInfo;
use model\User;
use util\cache\CacheIoManager;
use util\client\PersistentStore;
use util\DbWrapper;
use util\encryption\EncryptionManager;

class LoginHandler extends GenericPDORequestHandler
{
    use DefaultRanAndSucceed;

    const LOGIN = "login";
    const PASSWORD = "password";

    const LOGIN_INFO = "LGI";

    const MESSAGE_NO_LOGIN = "Unknown User";
    const MESSAGE_BAD_CONNEXION = "Bad Login/Password";
    /**
     * @var clientStore
     */
    private $clientStore;
    /**
     * @var CacheIoManager
     */
    private $cacheIoManager;
    /**
     * @var EncryptionManager
     */
    private $encryptionManager;
    /** @var User */
    private $user;


    public function __construct(
        DbWrapper $wrapper,
        ClientStore $clientStore,
        CacheIoManager $cacheIoManager,
        EncryptionManager $encryptionManager
    ) {
        parent::__construct($wrapper);
        $this->clientStore = $clientStore;
        $this->cacheIoManager = $cacheIoManager;
        $this->encryptionManager = $encryptionManager;
    }

    /**
     * @param $login
     * @param $password
     *
     * @throws \Exception
     */
    public function run($login, $password)
    {
        $this->setRan();
        $contact = $this->fetchData($login);
        if (!$contact) {
            throw new \RuntimeException(self::MESSAGE_NO_LOGIN);
        }
        if (!password_verify($password, $contact->getPassword())) {
            throw new \RuntimeException(self::MESSAGE_BAD_CONNEXION);
        }
        self::giveIVToStore();
        $this->pushUserToCache($contact);
        $this->getUserFromCache($contact->getLogin());
        $this->pushInfoToStore($contact->getInfos());
        $this->setSuccess();
    }

    public function fetchData($login)
    {
        $stmt
            = $this->wrapper->run("SELECT user_id, login_value, password_hash, user_firstname, user_lastname "
            ."FROM USER_INFO NATURAL JOIN USERS NATURAL JOIN PASSWORDS NATURAL JOIN LOGINS "
            ."WHERE login_value = ? AND info_end_validity IS NULL",
            [$login]);

        $collection = $stmt->fetchAll();
        if (!count($collection)) {
            return false;
        }
        $value = $collection->first();
        $contact = new User($value->user_id, $value->user_lastname,
            $value->user_firstname, $value->login_value, $value->password_hash);

        return $contact;
    }

    /**
     * @throws \Exception
     */
    private function giveIVToStore()
    {
        $iv = $this->encryptionManager::makeNumericKey();
        $this->encryptionManager->setIv($iv);
        $this->clientStore[EncryptionManager::IV_NAME] = $iv;
    }

    public function pushUserToCache(User $contact)
    {
        $this->cacheIoManager[$contact->getLogin()] = serialize($contact);
    }

    public function getUserFromCache($login) : User
    {
        return unserialize($this->cacheIoManager[$login]);
    }

    /**
     * @return User
     */
    public function getUser() : User
    {
        return $this->user;
    }

    public function pushInfoToStore(LoginInfo $info)
    {
        $this->clientStore[self::LOGIN_INFO] = $this->encryptionManager->encrypt(serialize($info));
    }

    public function attemptCacheLogin()
    {
        if ($this->hasBeenRan()) {
            if ($this->succeeded()) {
                return $this->user;
            }

            return false;
        }
        $this->setRan();
        if (!self::checkStoreHasIV()) {
            return false;
        }
        if (!self::checkStoreHasInfo()) {
            return false;
        }
        self::giveIVToEncrypter();

        $login_info = self::getInfoFromStore();

        if (!self::checkCacheHasInfo($login_info->getLogin())) {
            $contact = $this->fetchData($login_info->getLogin());
            if (!$contact) {
                return false;
            }
            $this->pushUserToCache($contact);
        } else {
            $contact = self::getUserFromCache($login_info->getLogin());
        }

        if ($contact->getPassword() !== $login_info->getPassword()) {
            return false;
        }

        $this->setSuccess();
        $this->user = $contact;

        return $contact;
    }

    public function checkStoreHasIV()
    {
        return $this->clientStore->offsetExists(EncryptionManager::IV_NAME);
    }

    public function checkStoreHasInfo()
    {
        return $this->clientStore->offsetExists(self::LOGIN_INFO);
    }

    private function giveIVToEncrypter()
    {
        $this->encryptionManager->setIv($this->clientStore[EncryptionManager::IV_NAME]);
    }

    public function getInfoFromStore() : LoginInfo
    {
        return unserialize($this->encryptionManager->decrypt($this->clientStore[self::LOGIN_INFO]));
    }

    public function checkCacheHasInfo($login)
    {
        return $this->cacheIoManager->offsetExists($login);
    }

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitLogin($this);
    }
}