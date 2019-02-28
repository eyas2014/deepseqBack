<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function test(){

    	$content=file("output_s1.txt");
    	$too=json_decode($content[0]);
    	$max=0;

    	foreach($too as $key=>$boo){
            $max++;
            // $read=new Reads;
            // $read->position=$key;
            // $read->genome_reads=$boo;
            // $read->save();
    	}



    	$str="just started";
    	return view("welcome", ["str" => $max]);

    }

    public function chart($zoom=0, $start=0){

        $genome_size=4641000;
        $zoom_level=$zoom;
        $column_width=6**(5-$zoom_level);
        $start=$start;
        $end=$start+$column_width*1500;
        $NO_sample=2;
        $offset=floor($start/$column_width);

        for($i=0; $i<$NO_sample; $i++){
            $results[$i]=DB::connection('mysql')->select('select floor(genome_position/?) as genome_position, max(genome_reads) as genome_reads from genome_reads where genome_position>=? and genome_position<? and sample_id=? group by floor(genome_position/?)', [$column_width, $start, $end, $i+1, $column_width]);
        }

        if($genome_size>=$end){
            $chart=array_fill(0, 1500, array_fill(0,$NO_sample,0));
        }
        else {
            $total_columns=ceil(($genome_size-$start)/$column_width);
            $chart=array_fill(0, $total_columns, array_fill(0,$NO_sample,0));
        }

        for($i=0; $i<$NO_sample; $i++) {
            foreach($results[$i] as $result){
                $column_position=$result->genome_position-$offset;
                $chart[$column_position][$i]=$result->genome_reads;
            }
        }
        return $chart;
    }


    public function shift($zoom=0, $start=0){

        $genome_size=4641000;
        $zoom_level=$zoom;
        $column_width=6**(5-$zoom_level);
        $start=$start;
        $end=$start+$column_width*6;
        $NO_sample=2;
        $offset=floor($start/$column_width);

        for($i=0; $i<$NO_sample; $i++){
            $results[$i]=DB::connection('mysql')->select('select floor(genome_position/?) as genome_position, max(genome_reads) as genome_reads from genome_reads where genome_position>=? and genome_position<? and sample_id=? group by floor(genome_position/?)', [$column_width, $start, $end, $i+1, $column_width]);
        }

        if($genome_size>=$end){
            $shift=array_fill(0, 6, array_fill(0,$NO_sample,0));
        }
        else {
            $total_columns=ceil(($genome_size-$start)/$column_width);
            $shift=array_fill(0, $total_columns, array_fill(0,$NO_sample,0));
        }

        for($i=0; $i<$NO_sample; $i++) {
            foreach($results[$i] as $result){
                $column_position=$result->genome_position-$offset;
                $shift[$column_position][$i]=$result->genome_reads;
            }
        }
        return $shift;
    }
}
