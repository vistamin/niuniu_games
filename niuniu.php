<?php

//获取所有牌的排列组合,类似于C_5^3
function combination($arr, $m)
{
    $result = array();
    if ($m ==1)
    {
       return $arr;
    }
    
    if ($m == count($arr))
    {
        $result[] = implode(',' , $arr);
        return $result;
    }
        
    $temp_firstelement = $arr[0];
    unset($arr[0]);
    $arr = array_values($arr);
    $temp_list1 = combination($arr, ($m-1));
    
    foreach ($temp_list1 as $s)
    {
        $s = $temp_firstelement.','.$s;
        $result[] = $s;
    }
    unset($temp_list1);

    $temp_list2 = combination($arr, $m);
    foreach ($temp_list2 as $s)
    {
        $result[] = $s;
    }    
    unset($temp_list2);
    
    return $result;
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
function pai_sum_array($arr)
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

//针对5张牌,拿出其中的3张,然后计算牛几
function compute_niuji($left, $arr)
{
    $result1 = pai_sum_array($left);
    if($result1 == 10)
    {
        $right = array_diff($arr,$left);
        $result2 = pai_sum_array($right);
        $temp = $result1 * 10000 + $result2 * 100;
    }
    else
    {
        $temp = 0 * 10000 + 0 * 1000;
    }
    return $temp;
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

//计算这组数据中的最大的那张牌
function max_pai_in_arr($arr)
{
    $max_value = 0;
    foreach($arr as $value)
    {
        $max_value = max_pai_in_2($max_value, $value);
    }
    return $max_value;
}

//计算这组数据中最大得牛
function max_niuji_in_arr($arr, $left_len)
{
    $res = combination($arr, $left_len);//进行组合运算
    //针对每一组组合,统计其和
    foreach($res as $value)
    {
        $left = explode(",", $value);
        $niuji = compute_niuji($left, $arr);
        if ($niuji > $max_niuji)
            $max_niuji = $niuji;
    }
    $max_niuji = str_pad(strval($max_niuji + $max), 6, '0', STR_PAD_LEFT);
    return $max_niuji;
    
}

//在5张牌中有一张大王的情况下,计算最大值
function max_niuji_has_dawang($arr)
{
    $max_niuji = max_niuji_in_arr($arr, 3);
    if (intval($max_niuji) > 100000)
    {
        $max_niuji = "101000";
        return $max_niuji;
    }
    $max_niuji = max_niuji_in_arr($arr, 2);
    if (intval($max_niuji) > 100000)
    {
        $max_niuji = "101000";
        return $max_niuji;
    }
    //无法组成牛牛的情况下
    $res = combination($arr, 2);//进行组合运算
    foreach($res as $value)
    {
        print $value."\t";
        $left = explode(",", $value);
        $niuji = pai_sum_array($left);
        if ($niuji > $max_niuji)
            $max_niuji = $niuji;
    }
    $max_niuji = "100".$max_niuji."00";
    return $max_niuji;
}




//生成扑克牌和数字对应的表格
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
$poker[57]="小王";
$poker[58]="大王";





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
    //如果flag_sum = 0,表示一个王都没有拿到
    //如果flag_sum = 1,表示拿到了小王
    //如果flag_sum = 2,表示拿到了大王
    //如果flag_sum = 3,表示拿到了小王和大王
    $flag = 0;
    print ($j+1)."号玩家拿到的牌是:<br>";
    for($i=0;$i<5;$i++)
    {
        while(true)
        {
            $card=rand(1,58);
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
        print $poker[$card]."\t";
        if ($card == 57)
            $flag = $flag + 1;
        else if ($card == 58)
            $flag = $flag + 2;
        else
            //先不插入大小王到数组里面
            array_push($arr, $card);
        $result[$j][$i] = $card;
    }
    if ($flag == 0)
    {
        $max_niuji = max_niuji_in_arr($arr, 3);
        $max_pai = max_pai_in_arr($arr);
    }
    //拿到小王之后，小王可做黑桃6或者黑桃10.之所以用黑桃,是要取最大牌
    else if ($flag == 1)
    {
        $max_pai = max_pai_in_arr($arr);
        array_push($arr, 48);
        $max_niuji1 = max_niuji_in_arr($arr, 3);
        array_pop($arr);
        array_push($arr, 52);
        $max_niuji2 = max_niuji_in_arr($arr, 3);
        if (intval($max_niuji1) > intval($max_niuji2))
        {
            $max_niuji = $max_niuji1;
        }
        else
        {
            $max_niuji = $max_niuji2;
        }
    }
    //拿到大王之后,
    else if ($flag == 2)
    {
        $max_pai = max_pai_in_arr($arr);
        $max_niuji = max_niuji_has_dawang($arr);
    }
    //拿到大王和小王
    else if ($flag == 3)
    {
        $max_pai = max_pai_in_arr($arr);
        array_push($arr, 48);
        $max_niuji1 = max_niuji_has_dawang($arr);
        array_pop($arr);
        array_push($arr, 52);
        $max_niuji2 = max_niuji_has_dawang($arr);
        if (intval($max_niuji1) > intval($max_niuji2))
        {
            $max_niuji = $max_niuji1;
        }
        else
        {
            $max_niuji = $max_niuji2;
        }

    }
    
    print "<br>计算结果为:";
    if(substr($max_niuji,0,2) == "10")
    {
        if(substr($max_niuji,2,2) == "10")
            print "牛牛, 最大牌为".$poker[$max_pai];
        else
            print "牛".substr($max_niuji,3,1).", 最大牌为".$poker[$max_pai];
    }
    else
        print "没有牛, 最大牌为".$poker[$max_pai];

    print "<br><br>";

}

?>
