<?php
/**
 * Created by PhpStorm.
 * User: Pierre-Antoine
 * Date: 01/06/2018
 * Time: 15:17
 */

namespace handler\connexion;


use handler\GenericPDOHandler;
use handler\HandlerVisitor;
use model\Contact;
use model\LoginInfo;
use model\User;
use util\cache\CacheIoManager;
use util\client\ClientStore;
use util\DbWrapper;
use util\encryption\EncryptionManager;

class LoginHandler extends GenericPDOHandler
{

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
        $contact = $this->fetchData($login);
        if (!$contact) {
            throw new \RuntimeException(self::MESSAGE_NO_LOGIN);
        }
        if (!password_verify($password, $contact->getPassword())) {
            throw new \RuntimeException(self::MESSAGE_BAD_CONNEXION);
        }
        self::giveIVToStore();
        $this->cacheContact($contact);
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
        $contact = new Contact(new User($value->user_id, $value->user_lastname,
            $value->user_firstname), new LoginInfo($value->login_value, $value->password_hash));

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

    public function cacheContact(Contact $contact)
    {
        $this->cacheIoManager[$contact->getLogin()] = serialize($contact);
    }

    public function assignToStore(LoginInfo $info)
    {
        $this->clientStore[self::LOGIN_INFO] = $this->encryptionManager->encrypt(serialize($info));
    }

    public function attemptCacheLogin()
    {
        if (!self::checkStoreHasIV()) {
            return false;
        }
        if (!self::checkStoreHasInfo()) {
            return false;
        }
        self::giveIVToEncrypter();

        $login_info = self::retrieveFromStore();

        if (!self::checkCacheHasInfo($login_info->getLogin())) {
            $contact = $this->fetchData($login_info->getLogin());
            if (!$contact) {
                return false;
            }
            $this->cacheContact($contact);
        } else {
            $contact = self::retrieveFromCache($login_info->getLogin());
        }

        if ($contact->getPassword() !== $login_info->getPassword()) {
            return false;
        }

        return $contact;
    }

    public function checkStoreHasIV()
    {
        return isset($this->clientStore[EncryptionManager::IV_NAME]);
    }

    public function checkStoreHasInfo()
    {
        return isset($this->clientStore[self::LOGIN_INFO]);
    }

    private function giveIVToEncrypter()
    {
        $this->encryptionManager->setIv($this->clientStore[EncryptionManager::IV_NAME]);
    }

    public function retrieveFromStore() : LoginInfo
    {
        return unserialize($this->encryptionManager->decrypt($this->clientStore[self::LOGIN_INFO]));
    }

    public function checkCacheHasInfo($login)
    {
        return isset($this->cacheIoManager[$login]);
    }

    public function retrieveFromCache($login) : Contact
    {
        return unserialize($this->cacheIoManager[$login]);
    }

    public function accept(HandlerVisitor $visitor)
    {
        $visitor->visitLogin($this);
    }
}