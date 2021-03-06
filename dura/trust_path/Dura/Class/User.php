<?php

/**
 * A simple description for this script
 *
 * PHP Version 5.2.0 or Upper version
 *
 * @package    Dura
 * @author     Hidehito NOZAWA aka Suin <http://suin.asia>
 * @copyright  2010 Hidehito NOZAWA
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3
 *
 */

class Dura_Class_User
{
	protected $name = null;
	protected $icon = null;
	protected $id = null;
	protected $expire = null;
	protected $admin = false;
	protected $language = null;

	protected function __construct()
	{
	}

	public static function &getInstance()
	{
		static $instance = null;

		if ($instance === null)
		{
			$instance = new self();
		}

		return $instance;
	}

	public function login($name, $icon, $language, $admin = false, $password_room = null)
	{
		/*
		$this->name = $name;
		*/
		// bluelovers
		$this->_setName($name, false);
		// bluelovers
		$this->icon = $icon;
		$this->id = md5($name . getenv('REMOTE_ADDR'));
		$this->language = $language;
		$this->admin = $admin;

		$_lang = Dura_Model_Lang::getInstance();
		$_lang_list = $_lang->getList()->toArray();

		if (!array_key_exists($language, $_lang_list))
		{
			$this->language = $language = $_lang->acceptLang();
		}

		// bluelovers
		if (isset($password_room) && $password_room !== null)
		{
			$password_room = empty($password_room) ? 0 : (string )$password_room;

			$this->password_room = $password_room;
		}
		// bluelovers

		$_SESSION['user'] = $this;
	}

	public function loadSession()
	{
		if (isset($_SESSION['user']) and $_SESSION['user'] instanceof self)
		{
			$user = $_SESSION['user'];
			/*
			$this->name   = $user->name;
			*/
			// bluelovers
			$this->_setName($user->name, true);
			// bluelovers

			$this->icon = $user->icon;
			$this->id = $user->id;
			$this->expire = $user->expire;
			$this->id = $user->id;
			$this->language = $user->language;
			$this->admin = $user->admin;

			// bluelovers
			$this->password_room = $user->password_room;
			// bluelovers
		}
	}

	// bluelovers
	protected function _setName($name, $update_session = false)
	{
		$_name = htmlspecialchars(trim(htmlspecialchars_decode((string )$name)));
		$this->name = $_name;

		if ($update_session)
		{
			$_SESSION['user']->name = $_name;
		}
	}
	// bluelovers

	public function isUser()
	{
		return ($this->id !== null);
	}

	public function isAdmin()
	{
		if ($this->isUser())
		{
			return $this->admin;
		}

		return false;
	}

	public function getName()
	{
		if (!$this->isUser()) return false;

		return $this->name;
	}

	public function getIcon()
	{
		if (!$this->isUser()) return false;

		return $this->icon;
	}

	public function getId()
	{
		if (!$this->isUser()) return false;

		return $this->id;
	}

	public function getLanguage($auto = false)
	{
		return ($this->language || !$auto) ? $this->language : Dura_Model_Lang::getInstance()->acceptLang();
	}

	// bluelovers
	public function getColor()
	{

		if (!$this->isUser()) return false;

		if (!isset($this->color))
		{
			$this->_handler($this);
		}

		return $this->color;
	}

	public function &_handler(&$user)
	{
		$icon = $user->getIcon();

		if ($icon && empty($user->color))
		{
			$user->color = Dura_Class_Icon::getIconColor($user->icon);
		}

		return $user;
	}
	// bluelovers

	public function getExpire()
	{
		if (!$this->isUser()) return false;

		return $this->expire;
	}

	public function updateExpire()
	{
		$this->expire = time() + DURA_TIMEOUT;

		if (isset($_SESSION['user']) and $_SESSION['user'] instanceof self)
		{
			$_SESSION['user']->expire = $this->expire;
		}
	}

	// bluelovers
	public function getPasswordRoom()
	{

		if (!$this->isUser()) return false;

		return $this->password_room;
	}

	public function setPasswordRoom($password = 0)
	{

		if (!$this->isUser()) return false;

		$password = empty($password) ? 0 : (string )$password;
		$this->password_room = $password;

		if (isset($_SESSION['user']) and $_SESSION['user'] instanceof self)
		{
			$_SESSION['user']->password_room = $password;
		}
	}
	// bluelovers

}
