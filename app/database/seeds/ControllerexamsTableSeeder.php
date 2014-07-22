<?php
use Faker\Factory as Faker;
class ControllerexamsTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();
		DB::table('controller_exams')->truncate();
        $answers = ['a', 'b', 'c', 'd'];
        $cids = [1258369,1020958,939000,1275800,1179391,1029804,1023894,852099,1282683,1017951,1169430,1246455,810260,857129,1011180,1187920,1185295,1181029,1161346,1102087,1278048,1093114,1125465,1253785,908290,989429,1214386,811161,1289484,1053496,856101,1133036,1211539,1016995,824425,996646,1232575,847338,990322,897429,878508,1035677,1000488,812021,1250947,1256391,1093141,938671,830104,1084837,883451,816817,812224,1047142,1092265,1213327,1093659,1105326,1278945,1242111,911755,990345,1056976,811224,1180484,1005094,1008209,936354,973948,1188397,1156948,810727,1244630,1097238,1131815,814057,1295404,1108317,968516,810788,825464,1079820,1285031,863645,1261625,1004409,1120544,1088911,1077868,997868,1240047,961044,1120240,896249,1134052,1212405,925096,1112502,817551,1089447,816289,811752,1200203,1105594,1104391,825556,1029885,810483,822049,1284333,887155,1138007,939022,1104849,1055319,1243449,1286551,1115074,1240830,1179152,937702,836379,998318,1073761,810085,1288118,960263,1238979,1145296,1041890,974176,1289337,1288667];
        $sid = [1240047, 1544047, 1170055];
		foreach(range(1,250) as $c)
		{
        //set up the number of wrong answers and generate the incorrect answers
        $wrong = $faker->numberBetween(0,8);
        $wronga = "";
        $wrongq = "";
        for($i = 0; $i <= $wrong; $i++)
        {
            $wronga .= $faker->randomElement($answers) . ",";
            $wrongq .= $faker->numberBetween(0,20) . ",";
        }

			  $e = new Exam();
        $e->cid = $faker->randomElement($cids);
        $e->reviewed_by = $faker->randomElement($sid);
        $e->exam_id = $faker->numberBetween(0,6);
        $e->reviewed = $faker->boolean(80);
        $e->wrong_questions = $wrongq;
        $e->wrong_answers = $wronga;
        $e->total_questions = 20;
        $e->cert_id = $faker->numberBetween(1,5);
        $e->save();
		}
	}

}
