<?php
class IFCalender {
    // user's given date
    public $input_date = '';
    
    // gregorian date
    private $gregorian_date = '';
    
    // International fixed calendar date
    private $ifc_date = '';
    
    // month's array
    private $month_names = array("January", "February", "March", "April", "May", "June", "Midi", "July", "August", "September", "October", "November", "December");
    
    // conversion factors
    private $conversion_factors = array(0, 3, 3, 6, 8, 11, 13, 0, -12, -9, -7, -4, -2);
    
    // date created after calculation
    public $updated_date = '';
    
    /**
     * Function to set class values
     *
     * @Created	26-June-2018
     * @param	user's date
     * @return	none
     */
    function __construct($input_date = '') {
        
        if( trim($input_date) != '' ) {
            // do nothing
        } else {
            // set current date
            $input_date = date('Y-m-d');
        }
        
        $this->input_date = $input_date;
        
        // convert date
        $this->convert_date_format();
    }
    
    /**
     * Function to set date format
     *
     * @Created	26-June-2018
     * @param	none
     * @return	none
     */
    private function convert_date_format() {
        if ($this->input_date != '') {
            $this->input_date = date( "F d, Y", strtotime($this->input_date) );
        }
    }
    
    /**
     * Function to get day from given date
     *
     * @Created	26-June-2018
     * @param	none
     * @return	day of given date
     */
    private function get_day() {
        $exp_1 = explode(",", $this->input_date);
        
        if(is_array($exp_1) && count($exp_1) == 2) {
            $exp_2 = explode(" ", $exp_1[0]);
            
            if(is_array($exp_2) && count($exp_2) == 2) {
                return trim( $exp_2[1] );
            }
            return false;
        }
        return false;
    }
    
    /**
     * Function to get month from given date
     *
     * @Created	26-June-2018
     * @param	none
     * @return	month of given date
     */
    private function get_month() {
        $exp_1 = explode(",", $this->input_date);
        
        if(is_array($exp_1) && count($exp_1) == 2) {
            $exp_2 = explode(" ", $exp_1[0]);
            
            if(is_array($exp_2) && count($exp_2) == 2) {
                return trim( $exp_2[0] );
            }
            return false;
        }
        return false;
    }
    
    /**
     * Function to get year from given date
     *
     * @Created	26-June-2018
     * @param	none
     * @return	year of given date
     */
    private function get_year() {
        $exp_1 = explode(",", $this->input_date);
        
        if(is_array($exp_1) && count($exp_1) == 2) {
            return trim( $exp_1[1] );
        }
        return false;
    }
    
    /**
     * Function to get month number from given date
     *
     * @Created	26-June-2018
     * @param	none
     * @return	month number of given date
     */
    private function get_month_number() {
        $month = $this->get_month();
        $month_number = false;
        if(trim( $month ) != '') {
            $month = strtoupper($month);
            foreach($this->month_names as $month_index => $month_name) {
                if(strtoupper($month_name) == $month) {
                    $month_number = $month_index;
                    break;
                }
            }
            return $month_number;
        }
        return false;
    }
    
    /**
     * Function to set gregorian to ifc date
     *
     * @Created	26-June-2018
     * @param	none
     * @return	new updated date
     */
    public function get_new_date() {
        
        // convert date
        $this->greg_ifc_date();
        
        if ($this->updated_date != '') {
            return $this->updated_date;
        }
        return false;
    }
    
    /**
     * Function to set gregorian to ifc date
     *
     * @Created	26-June-2018
     * @param	none
     * @return	none
     */
    private function greg_ifc_date() {
        
        $year = $this->get_year();
        $month = $this->get_month();
        $month_number = $this->get_month_number();
        $day = $this->get_day();
        
        // set ifc date
        $day += $this->conversion_factors[$month_number];
        
        // if IFC date is greater than 28 and it
        // isn't the 29 day month
        if( $day > 28  && $month_number != 12 ) {
            // subtract 28 and go to the next month
            $day -= 28;
            $month = $this->month_names[($month_number+1)];
        }
        
        // if IFC date is less than 1
        if( $day < 1 ) {
            // subtract 28 and go to the next month
            $day += 28;
            $month = $this->month_names[($month_number-1)];
        }
        
        // sets IFCdate equal to final month and day
        $this->updated_date = $month. " ". $day;
        
        if( $this->updated_date == "December 31" ) {
            $this->updated_date = "Extra day";
        }
    }    
}

function pr($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

$date = '2013-10-17';

$ifc_class = new IFCalender($date);
$new_date = $ifc_class->get_new_date();
pr('date given : '.$date);
pr('converted date : '.$new_date);

?>