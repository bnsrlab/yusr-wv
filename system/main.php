<?php session_start();
class Main {
    public $data, $type, $setting, $cookie_options;

    public function __call($meth, $args)
    {
        $this->toast('warning', 'Method not found!: '.$meth);
    }

    public function token($key, $user)
    {
        return hash('ripemd256', $key.crc32($user).getenv('TOKEN'));
    }

    public function __construct($type = NULL)
    {
        $lines = file(dirBase.'system/.env');
        foreach($lines as $line){
            list($name, $value) = explode('=', $line, 2);
            putenv(sprintf('%s=%s', trim($name), trim($value)));
        }

        if(empty($_SESSION['lang'])) $_SESSION['lang'] = 'english';

        $this->cookie_options = array(
            'expires' => time() + (6 * 30 * 24 * 3600),
            'path' => '/',
            'domain' => $_SERVER['SERVER_NAME'],
            'secure' => false,
            'httponly' => true
        );

        $this->connect();
        $this->type = $type;
        $this->setting = mysqli_fetch_assoc($this->data->query("SELECT * FROM `tb_settings` LIMIT 1"));

        if($type == 'public'){
            if(isset($_COOKIE['idUser']) && isset($_COOKIE['access'])){
                header("location: ".urlBase.'pages/profile');
                exit(0);
            }
        } else if($type == 'private'){
            if(isset($_COOKIE['idUser']) && isset($_COOKIE['access'])){
                $sql = $this->data->prepare("SELECT `lgnKey` FROM `tb_users` WHERE `idUser` = ?");
                $sql->bind_param('s', $_COOKIE['idUser']); $sql->execute();
                $result = $sql->get_result();

                $token = $this->token($result->fetch_row()[0], $_COOKIE['idUser']);

                if($token != $_COOKIE['access']){
                    header("Location: ".urlBase.'pages/logout');
                    exit(0);
                }
            } else {
                if(isset($_SERVER['HTTP_COOKIE'])){
                    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                    foreach($cookies as $cookie){
                        $parts = explode('=', $cookie);
                        $name = trim($parts[0]);
                        if($name != 'PHPSESSID'){
                            setcookie($name, '', time() - 3600, '/');
                        }
                    }
                }
                header("Location: ".urlBase);
                exit(0);
            }
        }
    }

    public function connect()
    {
        $this->data = new mysqli(getenv('DB_SERVER'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));
    }

    public function clean($post){
        return array_map(function($p){ return (is_array($p)) ? array_map(function($a){ return (is_array($a)) ? array_map(array($this->data, 'real_escape_string'), $a) : $this->data->real_escape_string($a); }, $p) : $this->data->real_escape_string($p); }, $post);
    }

    public function toast($status, $message, $link = NULL)
    {
        echo ($link == 'development') ? $message : "<script>sessionStorage.setItem('toastColor', '$status'); sessionStorage.setItem('toastMessage', `$message`); window.location.href = '". urlBase.$link."'</script>";
        exit(0);
    }

	public function open(array $set, $css = [], $js = [])
    {
		echo "<!DOCTYPE HTML><html lang='en'><head>
        <meta charset='UTF-8'/>
        <meta name='language' content='en'/>
        <meta name='apple-mobile-web-app-capable' content='yes'/>
        <meta name='mobile-web-app-capable' content='yes'/>
        <meta name='description' content='".$this->setting['desc']."'/>
        <meta name='author' content='bnsrcnry'/>
        <meta name='robots' content='noindex, nofollow'/>
        <meta name='googlebot' content='noindex, nofollow'/>
        <meta name='google' content='notranslate'/>
        <meta name='theme-color' content='#ffffff'/>
        <meta name='msapplication-TileColor' content='#ffffff'/>
        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=no'/>
        <title>".$set[1]." | Yusr - Make Quran More Accessible.</title>

        <!-- <link rel='manifest' href='".urlBase."manifest.json'/> -->
        <link rel='icon' type='image/png' href='".urlBase."assets/img/favicon/icon-72x72.png'/>
        <link rel='apple-touch-icon' href='".urlBase."assets/img/favicon/icon-96x96.png'/>

        <link rel='stylesheet' href='".urlBase."assets/css/dist/custom.css'/>
        <link rel='stylesheet' href='".urlBase."assets/css/dist/main.css'/>

        <script src='".urlBase."assets/js/lib/jquery.js'></script>
        <script src='".urlBase."assets/js/standard.js'></script>
        <!-- <script src='".urlBase."assets/js/app.js'></script> -->";

        if(!empty($css)){
            foreach($css as $file){
                echo "<link rel='stylesheet' href='".urlBase."assets/css/dist/$file.css'/>";
            }
        }

        if(!empty($js)){
            foreach($js as $file){
                echo "<script src='".urlBase."assets/js/$file.js'></script>";
            }
        }

        require dirBase.'system/parts/header.php';
	}

    public function upload_img($source, $destination, $quality)
    {
        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg'){
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, $quality);
        } else if ($info['mime'] == 'image/png'){
            $image = imagecreatefrompng($source);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagepng($image, $destination);
        }
    }

    public function upload_doc($source, $destination, $link = NULL, $quality = 80)
    {
        if(!in_array($source['type'], array('image/jpeg', 'image/png', 'video/webm', 'video/mp4', 'image/gif', 'application/pdf', 'application/excel')) || $source['size'] > 31457280){
            if($link){
                $this->toast('warning', 'Data type or file size exceeds the rule!', $link);
            } else {
                $data = array(
                    'status' => 'error',
                    'message' => 'Data type or file size exceeds the rule!'
                );

                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode($data);
                exit(0);
            }
        }

        if(preg_match('/image\/*/',$source['type'])){
            $info = getimagesize($source['tmp_name']);
            if ($info['mime'] == 'image/jpeg'){
                $image = imagecreatefromjpeg($source['tmp_name']);
                imagejpeg($image, $destination, $quality);
            } else if ($info['mime'] == 'image/png'){
                $image = imagecreatefrompng($source['tmp_name']);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                imagepng($image, $destination);
            }
        } else {
            move_uploaded_file($source['tmp_name'], $destination);
        }
    }

	public function close()
    {
		require dirBase.'system/parts/footer.php';
	}
}