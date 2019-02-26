<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Reads;

class CustomAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
  
    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $signature = 'customaction:do';

    protected $description = 'this is a custom action';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle(){
        $zoom=1;
        $start=0;

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
            $chart=array_fill(0, 6, array_fill(0, 250, array_fill(0,$NO_sample,0)));
        }
        else {
            $total_columns=floor(($genome_size-$start+1)/$column_width);
            $total_panels=ceil($total_columns/250);
            $chart=array_fill(0, $total_panels, array_fill(0, 250, array_fill(0,$NO_sample,0)));
        }

        for($i=0; $i<$NO_sample; $i++) {
            foreach($results[$i] as $result){
                $panel=floor(($result->genome_position-$offset)/250);
                $column=($result->genome_position-$offset)%250;
                $chart[$panel][$column][$i]=$result->genome_reads;
            }
        }

    }

    public function handledf(){
        $content=file("public/output_s1.txt");
        $result=json_decode($content[0]);
        $max=0;
        foreach ($result as $key => $value) {
            $max++;
            if($max%10000==0)echo $max;
            Reads::create(["genome_reads"=>$value, "sample_id"=>1, "genome_position"=>$key]);
        }
       


    }


    public function handleys(){
        $genome_size=4641000;

        $zoom_level=3;
        $column_width=6**(5-$zoom_level);
        $start=1000000;
        $end=$start+$column_width*1500;
        $NO_sample=2;

        $list=array();

        echo time();

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

        echo time();


    }



    public function handlex()
    {
        $genome_size=4641000;

        $zoom_level=2;
        $column_width=6**(5-$zoom_level);
        $start=1500000;
        $end=$start+$column_width*1500;


        $record=Reads::where("position", "<", $end)
                    ->where("position", ">=", $start)
                    ->chunk(30000, function($results) use($start, $column_width, &$list){
                        foreach($results as $record){
                            $index=floor(($record->position-$start)/$column_width);
                            if(!isset($list[$index]))$list[$index]=0;
                            $list[$index]=max($record->genome_reads,$list[$index]);
                        }
                    });

        if($genome_size>=$end)$total_columns=1500;
        else $total_columns=floor(($genome_size-$start)/$column_width)+1;

        for($i=0; $i<$total_columns; $i++){
            if(!isset($list[$i]))$list[$i]=0;
            $panel=floor($i/250);
            $column=$i%250;
            $chart[$panel][$column]=$list[$i];
        }

        foreach($chart as $panel){
            foreach($panel as $column){
                echo $column;
                echo " ";

            }
            echo "---------------------------------------------\n";

        }
    }

}
