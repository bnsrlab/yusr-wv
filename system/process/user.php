<?php require dirname(__FILE__, 3).'/root.php';
require dirBase.'vendor/autoload.php';
require dirBase.'system/main.php';

class Member extends Main {
    public function register($post)
    {
        parent::__construct('public');
        $post = parent::clean($post);

        $email = strtolower($post['email']);
        $password = hash('ripemd256', trim($post['password']));
        $idMember = ($sql = mysqli_fetch_assoc($this->data->query("SELECT `idUser` FROM `tb_users` ORDER BY `idUser` DESC LIMIT 1"))) ? 'user-'.date('jny').'-'.sprintf('%05d', (intval(substr($sql['idUser'], -5)) + 1)) : 'member-'.date('jny').'-00001';

        $this->data->query("INSERT INTO tb_members(`idMember`, `idEdu`, `idChurch`, `churchName`, `name`, `nickname`, `gender`, `placeofBirth`, `dateOfBirth`, `bloodType`, `readyToDonate`, `address`, `rt`, `rw`, `zipCode`, `province`, `city`, `nationality`, `email`, `phone1`, `phone2`, `mobilePhone1`, `mobilePhone2`, `whatsapp1`, `whatsapp2`, `password`, `verifiedEmailCode`, `created`, `modified`) VALUES()");

        if($this->data->affected_rows > 0){
            $body = "";

            $this->sendMail($email, $post['fullname'], 'Verify email Address', $body);

            $resp = array(
                'status' => 'success',
                'message' => 'Registrasi berhasil, silahkan login atau cek email Anda untuk verifikasi email.'
            );
        } else {
            $resp = array(
                'status' => 'error',
                'message' => 'Registrasi gagal, silahkan coba kembali atau hubungi kami untuk bantuan!'
            );
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($resp);
        exit(0);
    }

    public function editProfile($post)
    {
        parent::__construct('private');
        $post = parent::clean($post);

        $email = strtolower($post['email']);
        $idMember = $this->data->real_escape_string($_COOKIE['idMember']);
        $dateOfBirth = date('Y-m-d', strtotime(str_replace('-', '/', $post['dateOfBirth'])));

        if(!empty($post['img']['tmp_name'])){
            $img = $idMember.'.'.pathinfo($post['img']['name'], PATHINFO_EXTENSION);
            $this->upload($post['img'], dirBase.'assets/img/members/'.$img);
            $this->data->query("UPDATE `tb_members` SET `img` = '$img' WHERE `idMember` = '$idMember'");
        }

        if($this->data->query("UPDATE `tb_members` SET `name` = '{$post['fullname']}', `nickname` = '{$post['nickname']}', `gender` = '{$post['gender']}', `placeOfBirth` = '{$post['placeOfBirth']}', `dateOfBirth` = '$dateOfBirth', `bloodType` = '{$post['bloodType']}', `readyToDonate` = '{$post['readyToDonate']}', `address` = '{$post['address']}', `rt` = '{$post['rt']}', `rw` = '{$post['rw']}', `zipCode` = '{$post['zipCode']}', `province` = '{$post['province']}', `city` = '{$post['city']}', `email` = '$email', `phone1` = '{$post['phone1']}', `phone2` = '{$post['phone2']}', `mobilePhone1` = '{$post['mobilePhone1']}', `mobilePhone2` = '{$post['mobilePhone2']}', `whatsapp1` = '{$post['whatsapp1']}', `whatsapp2` = '{$post['whatsapp2']}', `descEducation` = '{$post['descEducation']}' where `idMember` = '$idMember'")) {
            $resp = array(
                'status' => 'success',
                'message' => 'Ubah data profil Anda berhasil.'
            );
        } else {
            $resp = array(
                'status' => 'error',
                'message' => 'Ubah data profil Anda gagal!'
            );
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($resp);
        exit(0);
    }

    public function resend()
    {
        parent::__construct('private');
        $code = substr(md5(mt_rand()), 0, 10);
        $idMember = $this->data->real_escape_string($_COOKIE['idMember']);

        if($data = mysqli_fetch_assoc($this->data->query("SELECT `name`, `email` FROM tb_members WHERE `idMember` = '$idMember'"))){
            $this->data->query("UPDATE `tb_members` SET `verifiedEmailCode` = '$code' WHERE `idMember` = '$idMember'");

            $body = "<!DOCTYPE html><html lang='en' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:v='urn:schemas-microsoft-com:vml'><head><title></title><meta content='text/html; charset=utf-8' http-equiv='Content-Type'/><meta content='width=device-width, initial-scale=1.0' name='viewport'/><link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'/><style>*{box-sizing: border-box;}body{margin: 0;padding: 0;}a[x-apple-data-detectors]{color: inherit !important;text-decoration: inherit !important;}#MessageViewBody a{color: inherit;text-decoration: none;}p{line-height: inherit}.desktop_hide,.desktop_hide table{mso-hide: all;display: none;max-height: 0px;overflow: hidden;}@media (max-width:620px){.desktop_hide table.icons-inner{display: inline-block !important;}.icons-inner{text-align: center;}.icons-inner td{margin: 0 auto;}.row-content{width: 100% !important;}.mobile_hide{display: none;}.stack .column{width: 100%;display: block;}.mobile_hide{min-height: 0;max-height: 0;max-width: 0;overflow: hidden;font-size: 0px;}.desktop_hide,.desktop_hide table{display: table !important;max-height: none !important;}}</style></head><body style='margin: 0; background-color: #ffffff; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;'><table border='0' cellpadding='0' cellspacing='0' class='nl-container' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='heading_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='pad' style='padding-top:40px;text-align:center;width:100%;'><h1 style='margin: 0; color: #333; direction: ltr; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 64px; font-weight: 700; letter-spacing: -2px; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;'><span class='tinyMce-placeholder'>Welcome.</span></h1></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-2' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; border-radius: 0; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='paragraph_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;' width='100%'><tr><td class='pad' style='padding-bottom:10px;padding-left:20px;padding-right:20px;padding-top:10px;'><div style='color:#101112;direction:ltr;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;font-size:16px;font-weight:400;letter-spacing:0px;line-height:150%;text-align:center;mso-line-height-alt:24px;'><p style='margin: 0;'>Thank you for joining the discipleship. The next step is to verify your email, click the button below to verify.</p></div></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-3' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; border-radius: 0; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='button_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='pad' style='text-align:center;'><div align='center' class='alignment'><a href='".urlBase."pages/verified?email=".$data['email']."&code=".$code."'><div style='text-decoration:none;display:inline-block;color:#ffffff;background-color:#333;border-radius:0px;width:auto;border-top:0px solid transparent;font-weight:400;border-right:0px solid transparent;border-bottom:0px solid transparent;border-left:0px solid transparent;padding-top:10px;padding-bottom:10px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;text-align:center;mso-border-alt:none;word-break:keep-all;'><span style='padding-left:20px;padding-right:20px;font-size:16px;display:inline-block;letter-spacing:normal;'><span dir='ltr' style='word-break: break-word; line-height: 32px;'><strong>Verify Your Email Address.</strong></span></span></div></a></div></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-4' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; border-radius: 0; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 10px; padding-bottom: 10px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='10' cellspacing='0' class='divider_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='pad'><div align='center' class='alignment'><table border='0' cellpadding='0' cellspacing='0' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='divider_inner' style='font-size: 1px; line-height: 1px; border-top: 1px solid #CCCCCC;'><span> </span></td></tr></table></div></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-5' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='text_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;' width='100%'><tr><td class='pad' style='padding-bottom:10px;padding-left:10px;padding-right:10px;padding-top:15px;'><div style='font-family: sans-serif'><div class='' style='font-size: 12px; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 18px; color: #b2b2b2; line-height: 1.5;'><p style='margin: 0; font-size: 12px; text-align: center; mso-line-height-alt: 18px;'><span style='color:#c0c0c0;'><span style='font-size:16px;'>Discipleship is an application that answer the needs of various congregations and churches. © All rights reserved 2022.</span><br/><br/>Visit <a href='https://discipleship.id' rel='noopener' style='text-decoration: underline; color: #b2b2b2;' target='_blank'>https://discipleship.id</a></span></p></div></div></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-6' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='icons_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='pad' style='vertical-align: middle; color: #9d9d9d; font-family: inherit; font-size: 15px; padding-bottom: 5px; padding-top: 5px; text-align: center;'><table cellpadding='0' cellspacing='0' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'></table></td></tr></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></body></html>";
            $this->sendMail($data['email'], $data['name'], 'Resend Verify email Address', $body);

            $resp = array(
                'status' => 'success',
                'message' => 'Email verifikasi sudah berhasil dikirim ke '.$data['email'].' silahkan cek inbox Anda.'
            );
        } else {
            $resp = array(
                'status' => 'error',
                'message' => 'Email verifikasi gagal dikirim, silahkan coba kembali.'
            );
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($resp);
        exit(0);
    }

    public function checkEmail($post)
    {
        parent::__construct('public');
        $post = parent::clean($post);

        $resp = array(
            'status' => (mysqli_num_rows($this->data->query("SELECT `created` FROM `tb_members` WHERE `email` = '{$post['email']}'"))) ? 'success' : 'error',
            'message' => ''
        );

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($resp);
        exit(0);
    }

    public function login($post)
    {
        parent::__construct('public');
        $post = parent::clean($post);

        $password = hash('ripemd256', $post['password']);

        if($member = mysqli_fetch_assoc($this->data->query("SELECT `idMember`, `verifiedEmail`, `blocked` FROM `tb_members` WHERE `email` = '{$post['email']}' AND `password` = '$password'"))){

            if($member['verifiedEmail'] == 'n'){
                $resp = array(
                    'status' => 'error',
                    'message' => 'Silahkan verifikasi email Anda terlebih dahulu untuk dapat login ke akun Anda.'
                );
            } else if($member['blocked'] == 'y'){
                $resp = array(
                    'status' => 'error',
                    'message' => 'Akun Anda terblokir, silahkan hubungi kami atau pihak Gereja untuk bantuan'
                );
            } else {
                $key = substr(md5(mt_rand()), 0, 10);
                $token = $this->token2($key, $member['idMember']);
                $this->data->query("UPDATE `tb_members` SET `lgnKey` = '$key' WHERE idMember = '{$member['idMember']}'");

                setcookie('idMember', $member['idMember'], $this->cookie_options);
                setcookie('accessMember', $token, $this->cookie_options);

                $resp = array(
                    'status' => 'success',
                    'message' => ''
                );
            }
        } else {
            $resp = array(
                'status' => 'error',
                'message' => 'Email atau kata sandi tidak cocok, silahkan coba kembali.'
            );
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($resp);
        exit(0);
    }

    public function forgot($post)
    {
        parent::__construct('public');
        $post = parent::clean($post);

        if(!empty($post['email'])){
            if(mysqli_num_rows($this->data->query("SELECT modified FROM `tb_members` WHERE `email` = '{$post['email']}'"))){
                $token = hash('ripemd256', date('jnygis'));

                if($this->data->query("UPDATE `tb_members` SET `fgtToken` = '$token' WHERE `email` = '{$post['email']}'")){
                    $body = "<!DOCTYPE html><html lang='en' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:v='urn:schemas-microsoft-com:vml'><head><title></title><meta content='text/html; charset=utf-8' http-equiv='Content-Type'/><meta content='width=device-width, initial-scale=1.0' name='viewport'/><link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'/><style>*{box-sizing: border-box;}body{margin: 0;padding: 0;}a[x-apple-data-detectors]{color: inherit !important;text-decoration: inherit !important;}#MessageViewBody a{color: inherit;text-decoration: none;}p{line-height: inherit}.desktop_hide,.desktop_hide table{mso-hide: all;display: none;max-height: 0px;overflow: hidden;}@media (max-width:620px){.desktop_hide table.icons-inner{display: inline-block !important;}.icons-inner{text-align: center;}.icons-inner td{margin: 0 auto;}.row-content{width: 100% !important;}.mobile_hide{display: none;}.stack .column{width: 100%;display: block;}.mobile_hide{min-height: 0;max-height: 0;max-width: 0;overflow: hidden;font-size: 0px;}.desktop_hide,.desktop_hide table{display: table !important;max-height: none !important;}}</style></head><body style='margin: 0; background-color: #ffffff; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;'><table border='0' cellpadding='0' cellspacing='0' class='nl-container' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='heading_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='pad' style='padding-top:40px;text-align:center;width:100%;'><h1 style='margin: 0; color: #333; direction: ltr; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 64px; font-weight: 700; letter-spacing: -2px; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;'><span class='tinyMce-placeholder'>Hello.</span></h1></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-2' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; border-radius: 0; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='paragraph_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;' width='100%'><tr><td class='pad' style='padding-bottom:10px;padding-left:20px;padding-right:20px;padding-top:10px;'><div style='color:#101112;direction:ltr;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;font-size:16px;font-weight:400;letter-spacing:0px;line-height:150%;text-align:center;mso-line-height-alt:24px;'><p style='margin: 0;'>A request has been received to change the password for your Discipleship account. Click the button below to set a new password.</p></div></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-3' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; border-radius: 0; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='button_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='pad' style='text-align:center;'><div align='center' class='alignment'><a href='".urlBase."pages/reset?token=".$token."'><div style='text-decoration:none;display:inline-block;color:#ffffff;background-color:#333;border-radius:0px;width:auto;border-top:0px solid transparent;font-weight:400;border-right:0px solid transparent;border-bottom:0px solid transparent;border-left:0px solid transparent;padding-top:10px;padding-bottom:10px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;text-align:center;mso-border-alt:none;word-break:keep-all;'><span style='padding-left:20px;padding-right:20px;font-size:16px;display:inline-block;letter-spacing:normal;'><span dir='ltr' style='word-break: break-word; line-height: 32px;'><strong>Reset Password.</strong></span></span></div></div></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-4' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; border-radius: 0; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 10px; padding-bottom: 10px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='10' cellspacing='0' class='divider_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='pad'><div align='center' class='alignment'><table border='0' cellpadding='0' cellspacing='0' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='divider_inner' style='font-size: 1px; line-height: 1px; border-top: 1px solid #CCCCCC;'><span> </span></td></tr></table></div></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-5' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='text_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;' width='100%'><tr><td class='pad' style='padding-bottom:10px;padding-left:10px;padding-right:10px;padding-top:15px;'><div style='font-family: sans-serif'><div class='' style='font-size: 12px; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; mso-line-height-alt: 18px; color: #b2b2b2; line-height: 1.5;'><p style='margin: 0; font-size: 12px; text-align: center; mso-line-height-alt: 18px;'><span style='color:#c0c0c0;'><span style='font-size:16px;'>Discipleship is an application that answer the needs of various congregations and churches. © All rights reserved 2022.</span><br/><br/>Visit <a href='https://discipleship.id' rel='noopener' style='text-decoration: underline; color: #b2b2b2;' target='_blank'>https://discipleship.id</a></span></p></div></div></td></tr></table></td></tr></tbody></table></td></tr></tbody></table><table align='center' border='0' cellpadding='0' cellspacing='0' class='row row-6' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tbody><tr><td><table align='center' border='0' cellpadding='0' cellspacing='0' class='row-content stack' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #ffffff; color: #000000; width: 600px;' width='600'><tbody><tr><td class='column column-1' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;' width='100%'><table border='0' cellpadding='0' cellspacing='0' class='icons_block block-1' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='pad' style='vertical-align: middle; color: #9d9d9d; font-family: inherit; font-size: 15px; padding-bottom: 5px; padding-top: 5px; text-align: center;'><table cellpadding='0' cellspacing='0' role='presentation' style='mso-table-lspace: 0pt; mso-table-rspace: 0pt;' width='100%'><tr><td class='alignment' style='vertical-align: middle; text-align: center;'></td></tr></table></td></tr></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></body></html>";
                    $this->sendMail($post['email'], "Member Discipleship", 'Reset Password.', $body);
                    
                    $resp = array(
                        'status' => 'success',
                        'message' => 'Email untuk atur ulang kata sandi telah dikirim.'
                    );
                } else {
                    $resp = array(
                        'status' => 'error',
                        'message' => 'Gagal atur ulang kata sandi, silahkan coba kembali!'
                    );
                }
            } else {
                $resp = array(
                    'status' => 'error',
                    'message' => 'Alamat email tidak ditemukan!'
                );
            }
        } else {
            $resp = array(
                'status' => 'error',
                'message' => 'Alamat email kosong/tidak valid!'
            );
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($resp);
        exit(0);
    }

    public function reset($post)
    {
        parent::__construct('public');
        $post = parent::clean($post);

        if(!empty($post['password1']) && !empty($post['password2']) && !empty($post['fgtToken'])){
            if($post['password1'] == $post['password2']){
                $pass2 = hash('ripemd256', trim($post['password2']));

                if($this->data->query("UPDATE `tb_members` SET `fgtToken` = '', `password` = '$pass2' WHERE `idMember` = '{$post['idMember']}' AND `fgtToken` = '{$post['fgtToken']}'")){
                    $resp = array(
                        'status' => 'success',
                        'message' => 'Ubah kata sandi baru berhasil.'
                    );
                } else {
                    $resp = array(
                        'status' => 'error',
                        'message' => 'Ubah kata sandi gagal, silahkan coba lagi.'
                    );
                }
            } else {
                $resp = array(
                    'status' => 'error',
                    'message' => 'Kata sandi tidak sama!'
                );
            }
        } else {
            $resp = array(
                'status' => 'error',
                'message' => 'Tidak boleh ada form yang kosong!'
            );
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($resp);
        exit(0);
    }
}

$action = new Member;
call_user_func_array(array($action, filter_input(INPUT_GET, 'method', FILTER_SANITIZE_SPECIAL_CHARS)), array(array_merge($_POST, $_FILES)));