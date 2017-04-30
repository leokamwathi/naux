<?php

$s = "https://maps.googleapis.com/maps/api/staticmap?size=500x260&path=enc%3AlstFo}n_FpAL`@AxCTj@wKb@sGZuHNgCDwADeBPmDp@}HVaBl@wCT_BP_Fp@}LV_EN}AZqBf@}BdBkG~@qAfAaA`DqCjC_BVQ\]R[HKRMf@IXM`@UhCuB~@}@`DwDdEoD`CwBfFgElGcFrCaClAgApAuAv@iAd@{@bD}G@WHWX_A@c@Cm@IYi@y@_CeDiAsAYSk@Uo@Qg@K{AUoAK}@Q_Aa@e@c@g@o@]o@w@eAo@y@Wa@Sm@]c@[YeHqDMa@?UPq@Vq@CGBQtCmJJa@AOQM]c@o@y@q@sAYk@{@cBs@aAaAy@wEsCwAy@u@WmKoDyDuAsE}A}BvD&key=AIzaSyDrw7vZP5NQ6gC9LPpxYL8AdEneojJKTpo";
echo($s."\n");
$r = addslashes($s);
echo($r."\n");
$r = str_replace("\\\\","\\\\\\",$r);
echo($r."\n");
$r = addslashes($s);
$r = addslashes($s);
$r = str_replace("\\\\","\\\\\\",$r);
echo($r."\n");
$r = str_replace("\\","\\\\\\",$s);
echo($r."\n");


 ?>
