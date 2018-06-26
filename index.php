<?php
class IFCalender {
    // user's given date
    public $input_date = '';
    
    // gregorian date
    private $gregorian_date = '';
    
    // International fixed calendar date
    private $ifc_date = '';
    
    // date format type (1 : Gregorian, 2 : International Fixed)
    private $date_format_type = '';
    
    // month's array
    public $month_names = array("January", "February", "March", "April", "May", "June", "Midi", "July", "August", "September", "October", "November", "December");
    
    // calender data
    public $calender = array();
    
    // calender starting year
    private $starting_year = 1990;
    
    // week day names
    private $weekday_names = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    
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
    function __construct($input_date = '', $date_format = '') {
        
        if( trim( $input_date ) != '' ) {
            // do nothing
        } else {
            // set current date
            $input_date = date( 'Y-m-d' );
        }
        
        if( trim( $date_format ) != '' ) {
            // do nothing
        } else {
            // set Gregorian date format
            $date_format = '1';
        }
        
        $this->input_date = $input_date;
        $this->date_format_type = $date_format;
        
        // generate calender
        $this->generate_calender();
        
        // convert date
        $this->convert_date_format();
    }
    
    /**
     * Function to generate calender
     *
     * @Created	26-June-2018
     * @param	none
     * @return	none
     */
    private function generate_calender() {
        $starting_year = $this->starting_year;
        
        $current_year = date('Y', strtotime( "+4 years" ));
        
        $data = array();
        
        $j = $leap_counter = 1; // counter
        
        for( $i = $starting_year; $i <= $current_year; $i++ ) {
            
            // check is leap year
            $is_leap_year = ( ( $i % 5 ) == 0 ) ? true : false;
            
            $data[ $i ]['leap_year'] = ( $is_leap_year ) ? 1 : 0;
            
            $month_count = count( $this->month_names );
            
            $starting_day = 1;
            
            $week_index = 0;
            
            foreach( $this->month_names as $a => $b ) {
                
                $new_index = $a + 1;
                
                // check is even month
                $is_even_month = ( ( $new_index % 2 ) == 0 ) ? true : false;
                
                $max_days = ( $is_even_month ) ? 21 : 22;
                
                // in leap year last month has less one day 
                if( $is_leap_year && ($a == ( $month_count - 1 ) ) ) {
                    $max_days = $max_days - 1;
                }
                
                // set max weeks in month
                $max_weeks = ( ( $max_days % 3 ) == 0 ) ? 3 : 4;
                
                $data[ $i ]['months'][ $b ][ 'max_days' ] = $max_days;
                $data[ $i ]['months'][ $b ][ 'max_weeks' ] = $max_weeks;
                
                // make calender
                for( $p = 1; $p <= $max_weeks; $p++ ) {
                    foreach( $this->weekday_names as $c => $d ) {
                        
                        if( $starting_day > $max_days ) {
                            continue;
                        }
                        
                        if($c >= $week_index) {
                            $d = $this->weekday_names[ $week_index ];
                            if($i == $this->starting_year && $a == 0 && $c == 0 && $p == 1) {
                                $week_index++;
                                continue;
                            } else {
                                if($starting_day <= $max_days) {
                                    if ( !isset($data[ $i ]['months'][ $b ][ 'calender' ][ $starting_day ]) ) {
                                        $data[ $i ]['months'][ $b ][ 'calender' ][ $starting_day ] = $d;
                                        $data[ $i ]['months'][ $b ][ 'weeks' ][ $d ][] = $starting_day;
                                    }
                                }
                            }
                            
                        } else {
                            $d = $this->weekday_names[ $week_index ];
                            $data[ $i ]['months'][ $b ][ 'calender' ][ $starting_day ] = $d;
                            $data[ $i ]['months'][ $b ][ 'weeks' ][ $d ][] = $starting_day;
                        }
                        $starting_day++;
                        $week_index = ($c + 1);
                        $week_index = ($week_index > 6) ? 0 : $week_index;
                    }
                }
                
                if($starting_day > $max_days) {
                    $starting_day = 1;
                }
                
            }
            
        }
        
        $this->calender = $data;
        
    }
    
    
    
    /**
     * Function to set date format
     *
     * @Created	26-June-2018
     * @param	none
     * @return	none
     */
    private function convert_date_format() {
        if ( $this->input_date != '' ) {
            $this->input_date = date( "F d, Y", strtotime( $this->input_date ) );
            $this->gregorian_date = date("l F d, Y", strtotime( $this->input_date ) );
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
        $exp_1 = explode( ",", $this->input_date );
        
        if( is_array( $exp_1 ) && count( $exp_1 ) == 2 ) {
            $exp_2 = explode( " ", $exp_1[0] );
            
            if( is_array( $exp_2 ) && count( $exp_2 ) == 2 ) {
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
        $exp_1 = explode( ",", $this->input_date );
        
        if( is_array( $exp_1 ) && count( $exp_1 ) == 2 ) {
            $exp_2 = explode( " ", $exp_1[0] );
            
            if( is_array( $exp_2 ) && count( $exp_2 ) == 2 ) {
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
        $exp_1 = explode( ",", $this->input_date );
        
        if( is_array( $exp_1 ) && count( $exp_1 ) == 2 ) {
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
            $month = strtoupper( $month );
            foreach( $this->month_names as $month_index => $month_name ) {
                if( strtoupper( $month_name ) == $month ) {
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
        $this->get_day_by_date();
        
        if ( $this->updated_date != '' ) {
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
    private function get_day_by_date() {
        
        $year = $this->get_year();
        $month = $this->get_month();
        $month_number = $this->get_month_number();
        $day = $this->get_day();
        
        // get proper date from calender
        $month_arr = isset( $this->calender[ $year ] ) ? $this->calender[ $year ] : array();
        
        if( !isset( $month_arr['months'] ) ) {
            $this->updated_date = "Please Enter Valid Date!";
        } else {
            
            $month_arr = isset( $this->calender[ $year ][ 'months' ][ $month ] ) ? $this->calender[ $year ][ 'months' ][ $month ] : array();
            
            if( !isset( $month_arr['max_days'] ) ) {
                $this->updated_date = "Invalid Month Entered!";
            } else {
                
                if($this->date_format_type == 2) {
                    $this->updated_date = $month. " ". $day;
                } else {
                
                    $max_days = $this->calender[ $year ][ 'months' ][ $month ]['max_days'];
                    // set ifc date
                    $day += $this->conversion_factors[ $month_number ];

                    // if IFC date is greater than $max_days and it
                    // isn't the $max_days+1 day month
                    if( $day > $max_days  && $month_number != 12 ) {
                        // subtract $max_days and go to the next month
                        $day -= $max_days;
                        $month = $this->month_names[ ( $month_number + 1 ) ];
                    }

                    // if IFC date is less than 1
                    if( $day < 1 ) {
                        // subtract $max_days and go to the next month
                        $day += $max_days;
                        $month = $this->month_names[ ( $month_number - 1 ) ];
                    }

                    // sets IFCdate equal to final month and day
                    $this->updated_date = $month. " ". $day;
                }
                if( $this->updated_date == "December 31" ) {
                    $this->updated_date = "Extra day";
                } else {
                    
                    
                    
                    if( isset( $month_arr[ 'calender' ][ $day ] ) ) {
                        $str = "On ".$month." ".$day.", ".$year.", day is ";
                        $str .= '<b>'.$month_arr[ 'calender' ][ $day ].'</b>';
                    } else {
                        $str = "No Data Found!";
                    }
                    $this->updated_date = $str;
                }
            }
        }
    }    
}


$input_date = isset($_GET['date']) ? $_GET['date'] : '';
$date_type = isset($_GET['date_type']) ? $_GET['date_type'] : '1';
$output = $month_names = '';

if($input_date!= '') {
    $ifc_class = new IFCalender($input_date, $date_type);
    $new_date = $ifc_class->get_new_date();
    $month_names = implode(", ", $ifc_class->month_names);
    $output = $new_date;
} else {
    $output = 'Please Enter Valid Date To Get Output!';
}


?>
<style>
    body {
        font-family: "arial";
        color: #000000;
        background: #f3f3f3;
        font-size: 13px;
        margin: 0;
        padding: 0;
    }
    h1 {
        text-align: center;
        padding: 10px;
    }
    table {
        padding: 10px;
        font-size: 13px;
        display: block;
        width: 500px;
        margin: 0 auto;
        background: #fafafa;
    }
    table td{
        padding: 7px;
    }
</style>
<form method="get" action="?date=<?php echo $input_date?>&date_type=<?php echo $date_type?>">
    <h1>Custom calendar Script</h1>
    <table border="0" cellpadding='2' cellspacing='2'>
        <tr>
            <td width="20%">Enter Date</td>
            <td><input type="text" name="date" value="<?php echo $input_date?>" /> (date Format : YYYY-mm-dd)</td>
        </tr>
        <tr style="display: none">
            <td>Input Date Type</td>
            <td><input type="radio" name="date_type" value="1" <?php echo $date_type == '1' ? "checked" : ''?> /> Gregorian Date <input type="radio" name="date_type" value="2" <?php echo $date_type == '2' ? "checked" : ''?> /> International Fixed Date</td>
        </tr>
        <tr>
            <td> </td>
            <td><input type="submit" name="submit" value="Get Day" /></td>
        </tr>
        <?php if( trim( $output ) != '' ){?>
        <tr>
            <td>Months</td>
            <td><?php echo $month_names?></td>
        </tr>
        <tr style="color:#ff0000">
            <td>Output</td>
            <td><?php echo $output?></td>
        </tr>
        <?php }?>
    </table>
</form>