<?php
require_once "vendor/autoload.php";

use phpseclib3\Crypt\RSA;

class SecManager {
    public mixed $private;
    public mixed $public;
    private string $private_key_filename = "private_rsa-4096.key";
    private string $public_key_filename = "public_rsa-4096.key";
    private bool $private_key_loaded = false;
    private bool $public_key_loaded = false;

    public function __construct() {
        $this->init_keys();
    }

    private function init_keys(): void
    {
        if(!is_file($this->private_key_filename)) {
            $fp = fopen($this->private_key_filename, "w");
            $this->private = RSA::createKey(4096);
            fwrite($fp, $this->private->__toString());
            fclose($fp);
        } else {
            $this->private = RSA::loadPrivateKey(file_get_contents($this->private_key_filename));
            $this->private_key_loaded = true;
        }


        if(!is_file($this->public_key_filename)) {
            $fp = fopen($this->public_key_filename, "w");
            fwrite($fp, $this->public->__toString());
            fclose($fp);
        } else {
            $content = file_get_contents($this->public_key_filename);
            $this->public = RSA::loadPublicKey($content);
            $this->public_key_loaded = true;
        }
    }

    public function decrypt_string($ciphertext_b64): string
    {
        if($this->private_key_loaded) {
            return $this->private->decrypt(base64_decode($ciphertext_b64));
        }
        return "";
    }

    public function encrypt_string($text): string
    {
        if($this->public_key_loaded) {
            return base64_encode($this->public->encrypt($text));
        }
        return base64_encode($this->private->getPublicKey()->encrypt($text));
    }
}

?>
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
        <label> Passwort:
            <input type="text" name="text_input">
        </label><input type="submit"><br>
    </form>
<?php

if(isset($_POST)) {

    if(isset($_POST["text_input"])) {
        $pass_clear = $_POST["text_input"];
        $sec_man = new SecManager();
        $encrypted = $sec_man->encrypt_string($pass_clear);
        $textarea = $encrypted.PHP_EOL.PHP_EOL. $pass_clear;
        $decrypted = $sec_man->decrypt_string($encrypted);
        ?>
        <style>
            .textarea_main {
                width: 76vw;
                min-height: 30vh;
                padding: .3em;
            }
        </style>
        <label>
        <textarea class="textarea_main"><?php echo $textarea; ?></textarea>
        </label><?php

    }

}
