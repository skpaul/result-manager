 <?php

//LAST MODIFIED: 2020-02-01 07:08 PM.

class AgeN{
    
    public function __construct($years=null, $months=null, $days=null, $totalNumberInDays = null) {
        $this->years = $years;
        $this->months = $months;
        $this->days = $days;
        $this->totalNumberInDays = $totalNumberInDays;
    }

    public $years;
    public $months;
    public $days;

    // Shows the total amount of days (not divided into years, months and days like above)
    public $totalNumberInDays;
}

/*
Insert null into Datetime field
===============================
Your problem here is that you're inserting 'NULL' surrounded in quotes, which makes it a string. Instead you need the bare NULL

 if (empty($_POST["date_field"]))
 {      
   $Date1 = NULL;
 }
 else
 {
   $Date1 = strtotime($_POST["date_field"]);
   $Date1 = date("Y-m-d H:i:s", $Date1);
 }

// Surround it in quotes if it isn't NULL.
if ($Date1 === NULL) {
  // Make a string NULL with no extra quotes
  $Date1 = 'NULL';
}
// For non-null values, surround the existing value in quotes...
else $Date1 = "'$Date1'";

// Later, inside your query don't use any additional quotes since you've already quoted it...
INSERT INTO Table1(date_field) VALUES($Date1);
*/




/*
OPTION 1:  date_create() + date_format combination
============ date_create() starts ==========================================
    Syntax
    ------
    date_create(time, timezone) : DateTime

    - Creates a DateTime object. 
    - The default value of the date/time string is current date/time.
    - This function is an alias of: DateTime::__construct().
    
    Parameters
    ----------
    time        :   (optional) Specifies a date/time string. NULL or default value indicates the current date/time.
    timezone    :   (optional) Time zone of the time. Asia/Dacca or	Asia/Dhaka	
    
    Return Value 
    ------------
    This function returns a new DateTime object which specifies a date.

    Example
    --------
    $date = date_create(); 
    $date = date_create("2018-03-15"); 
    $date = date_create('13-02-2013')

    FALSE/Exception on failure.
============ date_create() ends ==========================================

============ date_format() starts ==========================================
The date_format() function formats a given date. The date is supplied as DateTime instance which is generally returned by the date_create() function and format is a string according to which we want to format the date.

    Syntax:
    -------
    date_format(object, format): string

    Parameters 
    ----------
    This function accepts two parameters, all are mandatory.

    object : Specifies a DateTime object returned by date_create()
    format : Specifies the format for the date. It accepts the formats that is supported by date() function in PHP. Example – H(24-hr Format), h(12-hr Format), i(minutes:00 to 59), s(seconds: 00 to 59) etc.
    Return Value 
    ------------
    The date_format() function returns a string which represents the date formatted according to the specified format on successful formatting otherwise it returns false on faliure.
============ date_format() ends   ==========================================


OPTION 2:  strtotime() + date() combination
============ strtotime() starts ==========================================
    Description:
    Convert/Parse about any English textual date-time description to a UNIX timestamp. 
    The function accepts a string parameter in English which represents the description of date-time.

    Syntax:
    strtotime(string $english_datetime [, int $now = time() ] ) : int

    Parameters:
    - $english_datetime: English textual date-time description. mandatory.
            — $datetime can be 
                        "day-/month-/year hour:minute:second ampm", or
                        "year-/month-/day hour:minute:second ampm"
            — Example:  

    - $now: This parameter specifies the timestamp used to calculate the returned value. It is an optional parameter.

    Return value:
    Returns the time in seconds.

    Example:
    1. strtotime("now");                    //returns 1525378260, current time in second.
    2. strtotime("12th february 2017");     //returns 1486857600, the converted english text in second.
    3. strtotime("next sunday");            //returns 1525564800, the converted english text in second.
    4. strtotime("2010-12-23 23:20")        //returns 1293146400.
    5. strtotime("30-01-2019 23:20")        //returns 1548890400.
    6. strtotime("2010-12-23 1:20 pm");     //returns 1293110400.
    7. strtotime("25-12-2019 +1 days");     //returns 1577318400, add 1 day to 25-12-2019. Result 26-12-2019.

============ strtotime() ends ==========================================

============ date() starts =============================================
    Description:
    converts a UNIX integer timestamp to a more readable date and time format.

    Syntax:
    date(string $format [, int $timestamp = time() ] ) : string

    Parameters:
    - $format     :   The format of the returned date and time.
    - $timestamp  :   An integer Unix timestamp. Optional. If not given, then current date time will be used.

    Returns:
    String.
============ date() ends =============================================

============ time() starts ===========================================
    Used to get the current time as a Unix timestamp (the number of seconds).
============ time() ends =============================================

============ mktime() starts ===========================================
    Description:
    Get Unix timestamp for a specific date and time. If no date and time is provided,  the local date and time will be used.

    Syntax:
    mktime(hour, minute, second, month, day, year);
============ mktime() ends== ===========================================




Day---------------------------------------------------------------------------
d	Day of the month, 2 digits with leading zeros	01 to 31
D	A textual representation of a day, three letters	Mon through Sun
j	Day of the month without leading zeros	1 to 31
l   (lowercase 'L')	A full textual representation of the day of the week	Sunday through Saturday
N	ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0)	1 (for Monday) through 7 (for Sunday)
S	English ordinal suffix for the day of the month, 2 characters	st, nd, rd or th. Works well with j
w	Numeric representation of the day of the week	0 (for Sunday) through 6 (for Saturday)
z	The day of the year (starting from 0)	0 through 365

Week --------------------------------------------------------------------------
W	ISO-8601 week number of year, weeks starting on Monday	Example: 42 (the 42nd week in the year)

Month---------------------------------------------------------------------------
F	A full textual representation of a month, such as January or March	January through December
m	Numeric representation of a month, with leading zeros	01 through 12
M	A short textual representation of a month, three letters	Jan through Dec
n	Numeric representation of a month, without leading zeros	1 through 12
t	Number of days in the given month	28 through 31

Year---------------------------------------------------------------------------
L	Whether it's a leap year	1 if it is a leap year, 0 otherwise.
o	ISO-8601 week-numbering year. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead. (added in PHP 5.1.0)	Examples: 1999 or 2003
Y	A full numeric representation of a year, 4 digits	Examples: 1999 or 2003
y	A two digit representation of a year	Examples: 99 or 03

Time---------------------------------------------------------------------------
a	Lowercase Ante meridiem and Post meridiem	am or pm
A	Uppercase Ante meridiem and Post meridiem	AM or PM
B	Swatch Internet time	000 through 999
g	12-hour format of an hour without leading zeros	1 through 12
G	24-hour format of an hour without leading zeros	0 through 23
h	12-hour format of an hour with leading zeros	01 through 12
H	24-hour format of an hour with leading zeros	00 through 23
i	Minutes with leading zeros	00 to 59
s	Seconds with leading zeros	00 through 59
u	Microseconds (added in PHP 5.2.2). Note that date() will always generate 000000 since it takes an integer parameter, whereas DateTime::format() does support microseconds if DateTime was created with microseconds.	Example: 654321
*/




//The prefix "Swift" is used to avoid conflict with other namespaces.
class SwiftDatetime{

    private $_DateTimeZone = null;
   
    #region Constructor
    public function __construct($timezone = "Asia/Dhaka") {
        $this->_DateTimeZone = new DateTimeZone($timezone);
    }

    public function __destruct(){ }    
    #endregion


    #region private functions
    private function _setValue($value){
        try{
            if(isset($value)){
                if ($value instanceof \DateTime )
                    $this->_datetime = $value;
                else{
                    if ($value == "0000-00-00") 
                            $this->_datetime = NULL;
                        else
                            $this->_datetime = new DateTime($value, $this->_DateTimeZone);
                }
                   
            }
            else{
                $this->_datetime = NULL;
            }
        }
        catch(Exception $exp){
            throw $exp;
        }
    }

    private function _formatAs($format){
        
        if(isset($this->_datetime)){
            return $this->_datetime->format($format); 
        }
        else{
            return "";
        }
    }

    #endregion

    //===========START: Take input =====================



    /**
     * now()
     * 
     * Set current date and time
     */
    public function now(){
        $this->_setValue("now");
        return $this;
    }
    
    /**
     * value()
     * 
     * Set date or time or datetime
     * 
     * @param string $value. Default is "now". 
     */
    public function value($value="now"){
        $this->_setValue($value);
        return $this;
    }

  
    /**
     * asObject()
     * 
     * 
     * @return DateTime object
     */
    public function asObject(){
        return $this->_datetime;
    }

    /**
     * asFormat()
     * 
     * Returns current instance of php DateTime object as the specified $format.
     * 
     * Returns empty string ("") if instance value is null.
     * 
     * @param string $format 
     * @return DateTime object
     */
    public function asFormat($format){
        return $this->_formatAs($format);  
    }

    /**
     * asYmdHis()
     * 
     * Returns current instance of php DateTime object as 'Y-m-d H:i:s'.
     * 
     * Returns empty string ("") if instance value is null.
     * 
     * @return string
     */
    public function asYmdHis($separator = "-"){ 
        $format = "Y".$separator."m".$separator."d H:i:s";
        return $this->_formatAs($format);  
    }

    public function asdmYHis($separator = "-"){ 
        $format = "d".$separator."m".$separator."Y H:i:s";
        return $this->_formatAs($format);  
    }

    /**
     * asYmd()
     * 
     * Returns current instance of php DateTime object as 'Y-m-d'.
     * 
     * Returns "" if instance value is null.
     * 
     * @return string
     */
    public function asYmd($separator = "-"){ 
        $format = "Y".$separator."m".$separator."d";
        return $this->_formatAs($format);  
    }

    
    /**
     * asYmd()
     * 
     * Returns current instance of php DateTime object as 'm-d-Y'.
     * 
     * Returns "" if instance value is null.
     * 
     * @return string
     */
    public function asdmY($separator = "-"){ 
        
        $format = "m".$separator."d".$separator."Y";
        return $this->_formatAs($format);  
    }

    public function ashis(){ 
        $format = "h:i:s A";
        return $this->_formatAs($format);  
    }

    /**
     * addDays
     * 
     * Returns a new DateTime that adds the specified number of days to the value of this instance.
     * 
     * @param datetime days_to_add  add the specified days.
     * 
     * @return datetime in specified format.
     */
    public function addDays($days_to_add){
        //$d = strtotime("+$days_to_add days", strtotime($this->datetime));
        $this->_datetime->modify("+$days_to_add day");
        return $this;
    }

    //===========Start: Create a datetime value from given values =====================
    public function createDate($year = 0, $month = 0, $day =0, $hour =0, $minute =0, $second=0){
        //mktime(hour,minute,second,month,day,year)
        // $d=mktime(11, 14, 54, 8, 12, 2014);
        // echo "Created date is " . date("Y-m-d h:i:sa", $d);
    
        $this->_datetime = mktime($hour, $minute, $second, $month, $day, $year);
        return $this;
    }
    //===========End: Create a datetime value from given values =====================


    //====== START:: Replace Year, Month, Day, Hour and Minute of a Date/Datetime ==================================

    public function replaceYear($newYear){
        $existingMonth = $this->_datetime->format('m');
        $existingDay = $this->_datetime->format('d');
        $this->_datetime->setDate($newYear, $existingMonth, $existingDay);
        return $this;
    }

    public function replaceMonth($newMonth){
        $existingYear = $this->_datetime->format('Y');
        //$existingMonth = $this->_datetime->format('m');
        $existingDay = $this->_datetime->format('d');
        $this->_datetime->setDate($existingYear, $newMonth, $existingDay);
        return $this;
    }

    public function replaceDay($newDay){
        $existingYear = $this->_datetime->format('Y');
        $existingMonth = $this->_datetime->format('m');
        //$existingDay = $this->_datetime->format('d');
        $this->_datetime->setDate($existingYear, $existingMonth, $newDay);
        return $this;
    }

    public function replaceHour($newHour){
        $existingHour = $this->_datetime->format('H');
        $existingMinute = $this->_datetime->format('i');
        $existingSecond = $this->_datetime->format('s');
        $this->_datetime->setTime($newHour, $existingMinute, $existingSecond);
        return $this;
    }

    public function replaceMinute($newMinute){
        $existingHour = $this->_datetime->format('H');
        $existingMinute = $this->_datetime->format('i');
        $existingSecond = $this->_datetime->format('s');
        $this->_datetime->setTime($existingHour, $newMinute, $existingSecond);
        return $this;
    }

    public function replaceSecond($newSecond){
        $existingHour = $this->_datetime->format('H');
        $existingMinute = $this->_datetime->format('i');
        $existingSecond = $this->_datetime->format('s');
        $this->_datetime->setTime($existingHour, $existingMinute, $newSecond);
        return $this;
    }
    //====== END:: Replace Year, Month, Day, Hour and Minute of a Date/Datetime ==================================



    //====== START:: Separate Year, Month, Day, Hour and Minute from a Date/Datetime ==================================

    public function extractYear(){
        $existingValue = $this->_datetime->format('Y');
        return $existingValue;
    }
    public function extractMonth(){
        $existingValue = $this->_datetime->format('m');
        return $existingValue;
    }

    public function extractDay(){
        $existingValue = $this->_datetime->format('d');
        return $existingValue;
    }
    
    public function extractHour12(){
        $existingValue = $this->_datetime->format('h');
        return $existingValue;
    }
    
    public function extractHour24(){
        $existingValue = $this->_datetime->format('H');
        return $existingValue;
    }

    public function extractMinute(){
        $existingValue = $this->_datetime->format('i');
        return $existingValue;
    }
    //====== END:: Separate Year, Month, Day, Hour and Minute from a Date/Datetime ==================================
    

    
    public function extractSecond(){
        $existingValue = $this->_datetime->format('s');
        return $existingValue;
    }

    //====== start Utility methods ==================================
    public function getMonthList(){
        for ($i = 0; $i < 12; ++$i) {
            $m = date("F", strtotime("January +$i months"));
           echo $m . "<br>";
         }
    }

    /**
     * daysInMonth()
     * 
     * Get the number of days in a month for a specified year and calendar:
     * 
     * @param integer $month from 1 to 12.
     * @param int $year year value.
     * @return int days.
     */
    public function daysInMonth($month, $year){
        return cal_days_in_month(CAL_GREGORIAN,$month,$year);
    }
    
    //====== end Utility methods ==================================
}

?>