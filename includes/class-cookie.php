<?php
class PaCookie {
    protected $name; //nome del cookie
    protected $value=null; //valore del cookie
	protected $expire=0; //scadenza del cookie
	protected $path='/'; //percorso in cui è valido il cookie. se non specificato è valido per tutto il sito
    protected $domain=null; //dominio in cui il cookie è valido
    protected $secure=FALSE; //se il cookie deve essere trasmesso con https o no
    protected $httponly=TRUE; //se accessibile solo da http (se true non è accessibile da javascript)
	protected $samesite='Lax'; //None || Lax || Strict

    public function __construct($name) {
        $this->SetName($name);
    }
	
	public function CookieSave(){
		if(PHP_VERSION_ID < 70300) {
			return setcookie($this->name, $this->value, $this->expire, $this->path.'; samesite='.$this->samesite, $this->domain, $this->secure, $this->httponly);
		} else {
			$option= array(
                'expires' => $this->expire,
                'path' => $this->path,
                'domain' => $this->domain,
                'secure' => $this->secure,
                'httponly' => $this->httponly,
                'samesite' => $this->samesite // None || Lax  || Strict
                );
			return setcookie($this->name, $this->value, $option);
		}
	}
	
	public function CookieDelete(){
		$this->value=null;
		$this->expire=time()-42000;
		$this->CookieSave();
		unset($_COOKIE[$this->name]);
    }
	
	public function __toString() {
		$str='setcookie (name='.$this->GetName();
		$str.=', value='.$this->GetValue();
		$str.=', expire='.$this->GetExpire();
		
        if ($this->path!=='/') {
            $str.=', Path='.$this->path;
        } else {
			$str.=', Path="/"';
		}
		if ($this->getDomain()!==null) {
            $str.=', domain='.$this->GetDomain();
        } else {
			$str.=', domain=""';
		}
        if ($this->isSecure()===true) {
            $str.=', secure=true';
        } else {
			$str.=', secure=false';
		}
        if ($this->isHttpOnly()===true) {
            $str.=', httponly=true';
        } else {
			$str.=', httponly=false';
		}
		$str.=', samesite='.$this->GetSamesite();
		$str.=')';
		
        return $str;
    }

	
	protected function SetName($n){
		if($n=self::FilterText($n)) {
			$this->name=$n;
		} else {
			throw new InvalidArgumentException('Valore nome non valido');
		}
	}
	
	public function SetValue($v){
		$this->value=$v;
	}
	
	public function SetExpire($e){
		if(!is_numeric($e)) {
			$e=strtotime($e);
		}
		if (filter_var($e, FILTER_VALIDATE_INT) === 0 || filter_var($e, FILTER_VALIDATE_INT)) {
			$this->expire=$e;
		} else {
			throw new InvalidArgumentException('Data scadenza non valida');
		}
	}
	
	public function SetPath($p){
		if(file_exists(DATAROOT.$p)) {
			$this->path=$p;
		} else {
			throw new InvalidArgumentException('Valore path non valido');
		}
	}
	
	public function SetDomain($d){
		if($d=self::FilterDomain($d)) {
			$this->domain=$d;
		} else {
			throw new InvalidArgumentException('Valore Domain non valido');
		}
	}
	
	public function SetSecure($s){
		$this->secure=self::FilterBool($s);
	}
	
	public function SetHttponly($h){
		$this->httponly=self::FilterBool($h);
	}
	
	public function SetSameSite($s){
		$s=self::FilterText(strtolower($s));
		switch($s) {
			case 'none':
			$s='None';
			break;
			
			case 'strict':
			$s='Strict';
			break;
			
			default:
			$s='Lax';
			break;
		}
		$this->samesite=$s;
	}
	
	
	public function GetName() {
        return $this->name;
    }

    public function GetValue() {
        return $this->value;
    }

    public function GetDomain() {
        return $this->domain;
    }

    public function GetExpire() {
		return gmdate("D, d-M-Y H:i:s T", $this->expire);
    }

    public function GetPath() {
        return $this->path;
    }
   
    public function isSecure() {
        return $this->secure;
    }

    public function isHttpOnly() {
        return $this->httponly;
    }

    public function GetSamesite() {
		return $this->samesite;
	}
	
	
	public static function FilterDomain($d) {
		$d=trim($d);
		if(filter_var($d, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
			return $d;
		} else {
			return FALSE;
		}
	}
	
	public static function FilterBool($b){
		return filter_var($b, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
	}
	
	public static function FilterText($t){
		$t=trim(filter_var($t, FILTER_SANITIZE_STRING));
		if(is_string($t) && (strlen($t)>0) && preg_match('/^[a-zA-Z0-9_-]+$/', $t)) {
			return $t;
		} else {
			return FALSE;
		}
	}
}
?>
