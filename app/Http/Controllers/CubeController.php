<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Illuminate\Support\Facades\Input;

class CubeController extends Controller
{

    /**
     * Receives the initial and final x,y,z values and return the sumatory
     *
     * @return $cube = array[[[]]]
     */
    public static function sumCube($x1,$y1,$z1,$x2,$y2,$z2,$cube){
        $sum = 0;

        for( $x = $x1 ; $x <= $x2 ; $x++ ){
            for( $y = $y1 ; $y <= $y2 ; $y++ ){
                for( $z = $z1 ; $z <= $z2 ; $z++ ){

                    if( $cube[ $x ][ $y ][ $z ] != 0 ){
                        $sum += $cube[ $x ][ $y ][ $z ];
                    }

                }
            }
        }

        return $sum;
    }


    /**
     * Receives the cube size and set all its values to zero
     *
     * @return $cube = array[[[]]]
     */
    public static function setCube($matrixSize,$cube){

        for( $x = 1 ; $x <= $matrixSize ; $x++ ){
            for( $y = 1 ; $y <= $matrixSize ; $y++ ){
                for( $z = 1 ; $z <= $matrixSize ; $z++ ){
                    
                    $cube[ $x ][ $y ][ $z ] = 0;

                }
            }
        }

        return $cube;
    }

    /**
     * Index controller
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
    	return View::make('cubeView')
            ->with('input',null)
            ->with('output',null);
    }

    /**
     * Execute post controller
     *
     * @return \Illuminate\Http\Response
     */
    public function post(Request $request)
    {
        $input = Input::get('input');
        $output = "";
        $errorFlag = false;

        // Separate text into lines
        $textArray = explode("\n", $input);

        $testCases = $textArray[0];
        $pointer = 1;

        // Constraint 1 <= T <= 50
        if($testCases < 1 or $testCases >50){
            $output = "ERROR: Number of test cases must be between 1 and 50";
            $testCases = 0;
        }

        // Iterate until reach the indicated number of test cases
        for( $tcIterator = 0 ; $tcIterator < $testCases ; $tcIterator++ ){
            if($errorFlag){
                break;
            }

        	// Get Matrix size and number of operations
        	$nm = explode(' ', $textArray[$pointer]);
        	$matrixSize = $nm[0];
        	$operations = $nm[1];

            // Constraint 1 <= N <= 100
            if($matrixSize < 1 or $matrixSize > 100){
                $output = "ERROR: Cube size must be between 1 and 100";
                break;
            }

            // Constraint 1 <= N <= 100
            if($operations < 1 or $operations > 1000){
                $output = "ERROR: Number of operations must be between 1 and 1000";
                break;
            }

            // Initialize cube
            $cube = [[[]]];
            $cube = $this->setCube($matrixSize,$cube);

        	$pointer++;

            //Iterate operations
            for( $oIterator = 0 ; $oIterator < $operations; $oIterator++ ){
                // Separate indexes and values
                $operation = explode(" ", $textArray[$pointer]);

                // 1 <= x,y,z <= N 
                if($operation[1] < 1 or $operation[1] > $matrixSize or
                        $operation[2] < 1 or $operation[2] > $matrixSize or
                        $operation[3] < 1 or $operation[3] > $matrixSize){

                    $output = "ERROR: X,Y,Z values must be between 1 and ".$matrixSize;
                    $errorFlag = true;
                    break;
                }

                if($operation[0] === 'UPDATE'){
                    $x = $operation[1];
                    $y = $operation[2];
                    $z = $operation[3];
                    $value = $operation[4];

                    // Constraint -109 <= W <= 109
                    if ( $value < (pow(10,9)*(-1)) or $value > pow(10,9)){
                        $output = "ERROR: A setted value can't be greater than 10^9 or less than -10^9";
                        $errorFlag = true;
                        break;
                    }

                    $cube[ $x ][ $y ][ $z ] = $value;

                }else if ($operation[0] === 'QUERY'){
                    $x1 = $operation[1];
                    $y1 = $operation[2];
                    $z1 = $operation[3];
                    $x2 = $operation[4];
                    $y2 = $operation[5];
                    $z2 = $operation[6];

                    // Constraint 1 <= x1 <= x2 <= N  ; 1 <= y1 <= y2 <= N  ; 1 <= z1 <= z2 <= N 
                    if( $x1 > $x2 or $y1 > $y1 or $z1 > $z2){
                        $output = "ERROR: An initial x,y,z value is greater than its final value";
                        $errorFlag = true;
                        break;
                    }

                    $output .= $this->sumCube($x1,$y1,$z1,$x2,$y2,$z2,$cube) . "\n";

                }else{
                    $output .= "ERROR: Undefined operation ". $operation[0] ." on line ". $pointer."\n";
                    
                    $errorFlag = true;
                    break;
                }

                $pointer++;
            }
        }

        return View::make('cubeView')
        	->with('input',$input)
        	->with('output',$output);
    }

}
