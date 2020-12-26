<?php
require_once("CONSTANTS.php");

class Required{

    public static function ZeroSQL($version = null){
        require_once(ROOT_DIRECTORY ."/lib/database/ZeroSQL.php");
        return new static;
    }

    public static function SwiftDatetime($version = null){
        require_once(ROOT_DIRECTORY ."/lib/datetime/SwiftDatetime.php");
        return new static;
    }

    public static function EnDecryptor($version = null){
        require_once(ROOT_DIRECTORY . "/lib/endecryptor/EnDecryptor.php");
        return new static;
    }

    public static function Session($version = null){
        require_once(ROOT_DIRECTORY . "/lib/session/Session.php");
        return new static;
    }

    public static function SwiftLogger($version = null){
        require_once(ROOT_DIRECTORY ."/lib/logger/SwiftLogger.php");
        return new static;
    }

    public static function SwiftCSRF($version = null){
        require_once(ROOT_DIRECTORY ."/lib/CSRF/SwiftCSRF.php");
        return new static;
    }

    public static function SwiftJSON($version = null){
        require_once(ROOT_DIRECTORY ."/lib/JSON/SwiftJSON.php");
        return new static;
    }

    public static function FormValidator($version = null){
        require_once(ROOT_DIRECTORY ."/lib/FormValidator/FormValidator.php");
        return new static;
    }

    public static function Validable($version = null){
        require_once(ROOT_DIRECTORY ."/lib/FormValidator/Validable.php");
        return new static;
    }

    public static function Taka($version = null){
        require_once(ROOT_DIRECTORY ."/lib/Taka.php");
        return new static;
    }

    public static function With($version = null){
        require_once(ROOT_DIRECTORY ."/lib/With.php");
        return new static;
    }

    public static function Photo($version = null){
        require_once(ROOT_DIRECTORY ."/lib/Photo.php");
        return new static;
    }

    public static function UniqueCode($version = null){
        require_once(ROOT_DIRECTORY ."/lib/UniqueCode.php");
        return new static;
    }

    public static function AgeCalculator($version = null){
        require_once(ROOT_DIRECTORY ."/lib/AgeCalculator.php");
        return new static;
    }

    public static function Helpers($version = null){
        require_once(ROOT_DIRECTORY ."/lib/Helpers.php");
        return new static;
    }

    /**
     * The Heredoc.php file can't be included this way.
     * It must be called directly from the file.
    */
    public static function Heredoc($version = null){
        require_once(ROOT_DIRECTORY ."/lib/Heredoc.php");
        return new static;
    }

    public static function DropDown($version = null){
        require_once(ROOT_DIRECTORY ."/lib/dropdown/dropdown.php");
        return new static;
    }

    public static function RadioButton($version = null){
        require_once(ROOT_DIRECTORY ."/lib/radiobutton/radiobutton.php");
        return new static;
    }

    #region Partial pages
    public static function metaTags($version = null){
        require_once(ROOT_DIRECTORY . '/inc/meta_tags.html');
        return new static;
    }

    public static function favicon($version = null){
        require_once(ROOT_DIRECTORY . '/inc/favicon.php');
        return new static;
    }

    public static function footer($version = null){
        require_once(ROOT_DIRECTORY . '/inc/footer.php');
        return new static;
    }

    #endregion



    public static function teletalkCSS($version = null){
        $dateTime = (new DateTime("now", new DateTimeZone("Asia/Dhaka")))->format("Y-m-d-H-i-s");
        echo '<link href="'.BASE_URL.'assets/css/teletalk-css/teletalk.min.css?v='. $dateTime .'" rel="stylesheet">';
        return new static;
    }

    public static function gridCSS($version = null){
        echo ' <link href="'.BASE_URL.'assets/css/bootstrap-grid.css" rel="stylesheet">';
        return new static;
    }

    public static function jquery($version = null){
        echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>';
        return new static;
    }

    public static function hamburgerMenu($version = null){
        $dateTime = (new DateTime("now", new DateTimeZone("Asia/Dhaka")))->format("Y-m-d-H-i-s");
        echo '<script src="'.BASE_URL.'assets/js/hamburger-menu.js?v='.$dateTime.'"></script>';
        return new static;
    }

    public static function sweetModalJS($version = null){
        echo '<script src="'.BASE_URL.'assets/js/plugins/jquery.sweet-modal-1.3.3/jquery.sweet-modal.min.js"></script>';
        return new static;
    }

    public static function sweetModalCSS($version = null){
        echo '<link rel="stylesheet" href="'.BASE_URL.'assets/js/plugins/jquery.sweet-modal-1.3.3/jquery.sweet-modal.min.css">';
        return new static;
    }

    //mobileNumberParser is required for swift-submit.js
    public static function mobileNumberParser($version = null){
        echo '<script src="'.BASE_URL.'assets/js/mobile_number_parser.js"></script>';
        return new static;
    }
    

    //moment is required for swift-submit.js
    public static function moment($version = null){
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>';
        return new static;
    }

    /**
     * swiftForm()
     * 
     * Prerequisites - mobileNumberParser() and moment();
     */
    public static function swiftSubmit($version = null){
        $dateTime = (new DateTime("now", new DateTimeZone("Asia/Dhaka")))->format("Y-m-d-H-i-s");
        echo '<script src="'.BASE_URL.'assets/js/plugins/swift-submit/swift-submit-v2.js?v='.$dateTime.'"></script>';
        return new static;
    }

    public static function html2pdf($version = null){
        echo '<script src="'.BASE_URL.'assets/js/plugins/html2pdf/html2pdf.bundle.min.js"></script>';
        return new static;
    }

    public static function airDatePickerJS($version = null){
        echo '<script src="'.BASE_URL.'assets/js/plugins/air-datepicker/js/datepicker.min.js"></script>';
        // <!-- Include English language -->
        echo '<script src="'.BASE_URL.'assets/js/plugins/air-datepicker/js/i18n/datepicker.en.js"></script>';
        return new static;
    }

    public static function airDatePickerCSS($version = null){
        echo '<link href="'.BASE_URL.'assets/js/plugins/air-datepicker/css/datepicker.min.css" rel="stylesheet">';
        return new static;
    }
    
  
}

?>