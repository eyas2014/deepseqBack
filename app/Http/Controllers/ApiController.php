<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reads;

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

    public function api($zoom=0, $start=0){

        return $zoom."#".$start;

        $genome_size=4641000;

        $zoom_level=$zoom;
        $column_width=6**(5-$zoom_level);
        $start=$start;
        $end=$start+$column_width*1500;
        $NO_sample=2;

        $list=array();

        Reads::where("position", "<", $end)
                    ->where("position", ">=", $start)
                    ->chunk(30000, function($results) use($start, $column_width, &$list){
                        foreach($results as $record){
                            $index=floor(($record->position-$start)/$column_width);
                            $sample_id=$record->sample_id-1;
                            if(!isset($list[$index]))$list[$index]=[0,0];
                            if(!isset($list[$index][$sample_id]))$list[$index][$sample_id]=0;
                            $list[$index][$sample_id]=max($record->genome_reads,$list[$index][$sample_id]);
                        }
                    });

        if($genome_size>=$end)$total_columns=1500;
        else $total_columns=floor(($genome_size-$start)/$column_width)+1;

        for($i=0; $i<$total_columns; $i++){
            $panel=floor($i/250);
            $column=$i%250;
            for($j=0; $j<$NO_sample; $j++) {
                if(!isset($list[$i][$j]))$list[$i][$j]=0;
            }
            $chart[$panel][$column]=$list[$i];
        }

        return $chart;


    }
}
