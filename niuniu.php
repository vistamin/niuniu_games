<?php

//获取所有牌的排列组合,类似于C_5^3
function combination($arr, $len=0, $str="") {
    global $res;
    $arr_len = count($arr);
    if($len == 0){
        $res[] = $str;
    }else{
        for($i=0; $i<$arr_len-$len+1; $i++){
            $tmp = array_shift($arr);
            combination($arr, $len-1, $str.",".$tmp);
        }
    }
}

//判断是否为花牌
function is_huapai($pai)
{
    if(($pai>10 && $pai<14) || ($pai>24 && $pai<28) || ($pai>38 && $pai<42) || ($pai>52 && $pai<56))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}

//计算这组牌的和,结果要模10
function compute($arr)
{
    $sum = 0;
    foreach($arr as $a)
    {
        if(is_huapai($a))
        {
            $a = 10;
        }
        else
        {
            $a = $a%14;
        }
        $sum = $sum + $a;
    }

    if($sum%10 == 0)
        $result = 10;
    else
        $result = $sum%10;
    return $result;
}


//两张牌中比大小:花和花比较,花和非花比较,非花和非花比较
function max_pai_in_2($first, $second)
{
    //花与花,非花与非花比较
    if((is_huapai($first) && is_huapai($second)) || (!is_huapai($first) && !is_huapai($second)))
    {
        if ($first%14 > $second%14)
        {
            return $first;
        }
        else if ($first%14 < $second%14)
        {
            return $second;
        }
        //数字相同，则比较花色
        else if($first%14 == $second%14) 
        {
            if ($first > $second)
                return $first;
            else
                return $second;
        }
    }
    //非花与花比较
    else
    {
        if(is_huapai($first))
            return $first;
        else if(is_huapai($second))
            return $second;
    }
}

//计算这组数据中的最大牌
function max_pai_in_arr($arr)
{
    $max_value = 0;
    foreach($arr as $value)
    {
        $max_value = max_pai_in_2($max_value, $value);
    }
    return $max_value;
}


$i = 1;
$colors = array("梅花", "方块", "红桃", "黑桃");
$numbers = array("A", 2, 3, 4, 5, 6, 7, 8, 9, 10, "J", "Q", "K");
foreach($colors as $color)
{
    foreach($numbers as $number)
    {
        if($i%14 == 0)
            $i++;
        $poker[$i] = $color.$number;
        $i++;
    }
}
$poker[57]="大王";
$poker[58]="小王";





//$PEOPLE = $argv[1];
$PEOPLE = 4;
if ($PEOPLE >= 9)
{
    print "人数太多\n";
    exit(1);
}
$total = array();
$result = array();
for($j=0;$j<$PEOPLE;$j++)
{
    $arr = array();
    print ($j+1)."号玩家拿到的牌是:<br>";
    for($i=0;$i<5;$i++)
    {
        while(true)
        {
            $card=rand(1,56);
            if(array_key_exists($card,$total) || $card%14 == 0)
            {
                continue;
            }
            else
            {
                $total[$card]=1;
                break;
            }
        }
        $result[$j][$i] = $card;
        array_push($arr, $card);
        //print "card=".$card."\t";
        print $poker[$card]."\t";
    }
    $num=3;
    $res = array();//结果集
    combination($arr, $num);//进行排列运算
    $max = max_pai_in_arr($arr);
    foreach($res as $value)
    {
        $left = array_slice(explode(",", $value),1);
        $right = array_diff($arr,$left);
        $result1 = compute($left);
        if($result1 == 10)
        {
            $result2 = compute($right);
            $temp = $result1 * 10000 + $result2 * 100;
        }
        else
        {
            $temp = 0 * 10000 + 0 * 1000;
        }
        if ($temp > $result[$j]["niu"]["ji"])
        {
            $result[$j]["niu"]["ji"] = $temp;
        }
    }
    $result[$j]["niu"]["ji"] += $max;
    print "<br>计算结果为:";
    if(substr($result[$j]["niu"]["ji"],0,2) == "10")
    {
        if(substr($result[$j]["niu"]["ji"],2,2) == "10")
            print "牛牛, 最大牌为".$poker[$max];
        else
            print "牛".substr($result[$j]["niu"]["ji"],3,1).", 最大牌为".$poker[$max];
    }
    else
        print "没有牛, 最大牌为".$poker[$max];

    print "<br><br>";


}

?>
