
BETA
PHP Formatter
HomeFeaturesContactAbout
	

			
Welcome to the new PHP Formatter BETA!
We've given PHP Formatter a new design as well as a new engine! The new engine features:

    Blazingly fast, on the fly formatting of all scripts!
    PHP 4 and PHP 5 support
    Handy syntax check function
    Ability to create your own coding styles, or to use builtin styles
    Proper handling of doc comments, and alternative control structures



			
InputStyleFormat
1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62
63
64
65
66
67
68
69
70
71
72
73
74
75
76
77
78
79
80
81
82
83
84
85
86
87
88
89
90
91
92
93
94
95
96
97
98
99
100
101
102
103
104
105
106
107
108
109
110
111
112
113
114
115
116
117
118
119
120
121
122
123
124
125
126
127
128
129
130
131
132
133
134
135
136
137
138
139
140
141
142
143
144
145
146
147
148
149
150
151
152
153
154
155
156
157
158
159
160
161
162
163
164
165
166
167
168
169
170
171
172
173
174
175
176
177
178
179
180
181
182
183
184
185
186
187
188
189
190
191
192
193
194
195
196
197
198
199
200
201
202
203
204
205
206
207
208
209
210
211
212
213
214
215
216
217
218
219
220
221
222
223
224
225
226
227
228
229
230
231
232
233
234
235
236
237
238
239
240
241
242
243
244
245
246
247
248
249
250
251
252
253
254
255
256
257
258
259
260
261
262
263
264
265
266
267
268
269
270
271
272
273
274
275
276
277
278
279
280
281
282
283
284
285
286
287
288
289
290
291
292
293
294
295
296
297
298
299
300
301
302
303
304
305
306
307
308
309
310
311
312
313
314
315
316
317
318
319
320
321
322
323
324
325
326
327
328
329
330
331
332
333
334
335
336
337
338
339
340
341
342
343
344
345
346
347
348
349
350
351
352
353
354
355
356
357
358
359
360
361
362
363
364
365
366
367
368
369
370
371
372
373
374
375
376
377
378
379
380
381
382
383
384
385
386
387
388
389
390
391
392
393
394
395
396
397
398
399
400
401
402
403
404
405
406
407
408
409
410
411
412
413
414
415
416
417
418
419
420
421
422
423
424
425
426
427
428
429
430
431
432
433
434
435
436
437
438
439
440
441
442
443
444
445
446
447
448
449
450
451
452
453
454
455
456
457
458
459
460
461
462
463
464
465
466
467
468
469
470
471
472
473
474
475
476
477
478
479
480
481
482
483
484
485
486
487
488
489
490
491
492
493
494
495
496
497
498
499
500
501
502
503
504
505
506
507
508
509
510
511
512
513
514
515
516
517
518
519
520
521
522
523
524
525
526
527
528
529
530
531
532
533
534
535
536
537
538
539
540
541
542
543
544
545
546
547
548
549
550
551
552
553
554
555
556
557
558
559
560
561
562
563
564
565
566
567
568
569
570
571
572
573
574
575
576
577
578
579
580
581
582
583
584
585
586
587
588
589
590
591
592
593
594
595
596
597
598
599
600
601
602
603
604
605
606
607
608
609
610
611
612
613
614
615
616
617
618
619
620
621
622
623
624
625
626
627
628
629
630
631
632
633
634
635
636
637
638
639
640
641
642
643
644
645
646
647
648
649
650
651
652
653
654
655
656
657
658
659
660
661
662
663
664
665
666
667
668
669
670
671
672
673
674
675
676
677
678
679
680
681
682
683
684
685
686
687
688
689
690
691
692
693
694
695
696
697
698
699
700
701
702
703
704
705
706
707
708
709
710
711
712
713
714
715
716
717
718
719
720
721
722
723
724
725
726
727
728
729
730
731
732
733
734
735
736
737
738
739
740
741
742
743
744
745
746
747
748
749
750
751
752
753
754
755
756
757
758
759
760
761
762
763
764
765
766
767
768
769
770
771
772
773
774
775
776
777
778
779
780
781
782
783
784
785
786
787
788
789
790
791
792
793
794
795
796
797
798
799
800
801
802
803
804
805
806
807
808
809
810
811
812
813
814
815
816
817
818
819
820
821
822
823
824
825
826
827
828
829
830
831
832
833
834
835
836
837
838
839
840
841
842
843
844
845
846
847
848
849
850
851
852
853
854
855
856
857
858
859
860
861
862
863
864
865
866
867
868
869
870
871
872
873
874
875
876
877
878
879
880
881
882
883
884
885
886
887
888
889
890
891
892
893
894
895
896
897
898
899
900
901
902
903
904
905
906
907
908
909
910
911
912
913
914
915
916
917
918
919
920
921
922
923
924
925
926
927
928
929
930
931
932
933
934
935
936
937
938
939
940
941
942
943
944
945
946
947
948
949
950
951
952
953
954
955
956
957
958
959
960
961
962
963
964
965
966
967
968
969
970
971
972
973
974
975
976
977
978
979
980
981
982
983
984
985
986
987
988
989
990
991
992
993
994
995
996
997
998
999
1000
1001
1002
1003
1004
1005
1006
1007
1008
1009
1010
1011
1012
1013
1014
1015
1016
1017
1018
1019
1020
1021
1022
1023
1024
1025
1026
1027
1028
1029
1030
1031
1032
1033
1034
1035
1036
1037
1038
1039
1040
1041
1042
1043
1044
1045
1046
1047
1048
1049
1050
1051
1052
1053
1054
1055
1056
1057
1058
1059
1060
1061
1062
1063
1064
1065
1066
1067
1068
1069
1070
1071
1072
1073
1074
1075
1076
1077
1078
1079
1080
1081
1082
1083
1084
1085
1086
1087
1088
1089
1090
1091
1092
1093
1094
1095
1096
1097
1098
1099
1100
1101
1102
1103
1104
1105
1106
1107
1108
1109
1110
1111
1112
1113
1114
1115
1116
1117
1118
1119
1120
1121
1122
1123
1124
1125
1126
1127
1128
1129
1130
1131
1132
1133
1134
1135
1136
1137
1138
1139
1140
1141
1142
1143
1144
1145
1146
1147
1148
1149
1150
1151
1152
1153
1154
1155
1156
1157
1158
1159
1160
1161
1162
1163
1164
1165
1166
1167
1168
1169
1170
1171
1172
1173
1174
1175
1176
1177
1178
1179
1180
1181
1182
1183
1184
1185
1186
1187
1188
1189
1190
1191
1192
1193
1194
1195
1196
1197
1198
1199
1200
1201
1202
1203
1204
1205
1206
1207
1208
1209
1210
1211
1212
1213
1214
1215
1216
1217
1218
1219
1220
1221
1222
1223
1224
1225
1226
1227
1228
1229
1230
1231
1232
1233
1234
1235
1236
1237
1238
1239
1240
1241
1242
1243
1244
1245
1246
1247
1248
1249
1250
1251
1252
1253
1254
1255
1256
1257
1258
1259
1260
1261
1262
1263
1264
1265
1266
1267
1268
1269
1270
1271
1272
1273
1274
1275
1276
1277
1278
1279
1280
1281
1282
1283
1284
1285
1286
1287
1288
1289
1290
1291
1292
1293
1294
1295
1296
1297
1298
1299
1300
1301
1302
1303
1304
1305
1306
1307
1308
1309
1310
1311
1312
1313
1314
1315
1316
1317
1318
1319
1320
1321
1322
1323
1324
1325
1326
1327
1328
1329
1330
1331
1332
1333
1334
1335
1336
1337
1338
1339
1340
1341
1342
1343
1344
1345
1346
1347
1348
1349
1350
1351
1352
1353
1354
1355
1356
1357
1358
1359
1360
1361
1362
1363
1364
1365
1366
1367
1368
1369
1370
1371
1372
1373
1374
1375
1376
1377
1378
1379
1380
1381
1382
1383
1384
1385
1386
1387
1388
1389
1390
1391
1392
1393
1394
1395
1396
1397
1398
1399
1400
1401
1402
1403
1404
1405
1406
1407
1408
1409
1410
1411
1412
1413
1414
1415
1416
1417
1418
1419
1420
1421
1422
1423
1424
1425
1426
1427
1428
1429
1430
1431
1432
1433
1434
1435
1436
1437
1438
1439
1440
1441
1442
1443
1444
1445
1446
1447
1448
1449
1450
1451
1452
1453
1454
1455
1456
1457
1458
1459
1460
1461
1462
1463
1464
1465
1466
1467
1468
1469
1470
1471
1472
1473
1474
1475
1476
1477
1478
1479
1480
1481
1482
1483
1484
1485
1486
1487
1488
1489
1490
1491
1492
1493
1494
1495
1496
1497
1498
1499
1500
1501
1502
1503
1504
1505
1506
1507
1508
1509
1510
1511
1512
1513
1514
1515
1516
1517
1518
1519
1520
1521
1522
1523
1524
1525
1526
1527
1528
1529
1530
1531
1532
1533
1534
1535
1536
1537
1538
1539
1540
1541
1542
1543
1544
1545
1546
1547
1548
1549
1550
1551
1552
1553
1554
1555
1556
1557
1558
1559
1560
1561
1562
1563
1564
1565
1566
1567
1568
1569
1570
1571
1572
1573
1574
1575
1576
1577
1578
1579
1580
1581
1582
1583
1584
1585
1586
1587
1588
1589
1590
1591
1592
1593
1594
1595
1596
1597
1598
1599
1600
1601
1602
1603
1604
1605
1606
1607
1608
1609
1610
1611
1612
1613
1614
1615
1616
1617
1618
1619
1620
1621
1622
1623
1624
1625
1626
1627
1628
1629
1630
1631
1632
1633
1634
1635
1636
1637
1638
1639
1640
1641
1642
1643
1644
1645
1646
1647
1648
1649
1650
1651
1652
1653
1654
1655
1656
1657
1658
1659
1660
1661
1662
1663
1664
1665
1666
1667
1668
1669
1670
1671
1672
1673
1674
1675
1676
1677
1678
1679
1680
1681
1682
1683
1684
1685
1686
1687
1688
1689
1690
1691
1692
1693
1694
1695
1696
1697
1698
1699
1700
1701
1702
1703
1704
1705
1706
1707
1708
1709
1710
1711
1712
1713
1714
1715
1716
1717
1718
1719
1720
1721
1722
1723
1724
1725
1726
1727
1728
1729
1730
1731
1732
1733
1734
1735
1736
1737
1738
1739
1740
1741
1742
1743
1744
1745
1746
1747
1748
1749
1750
1751
1752
1753
1754
1755
1756
1757
1758
1759
1760
1761
1762
1763
1764
1765
1766
1767
1768
1769
1770
1771
1772
1773
1774
1775
1776
1777
1778
1779
1780
1781
1782
1783
1784
1785
1786
1787
1788
1789
1790
1791
1792
1793
1794
1795
1796
1797
1798
1799
1800
1801
1802
1803
1804
1805
1806
1807
1808
1809
1810
1811
1812
1813
1814
1815
1816
1817
1818
1819
1820
1821
1822
1823
1824
1825
1826
1827
1828
1829
1830
1831
1832
1833
1834
1835
1836
1837
1838
1839
1840
1841
1842
1843
1844
1845
1846
1847
1848
1849
1850
1851
1852
1853
1854
1855
1856
1857
1858
1859
1860
1861
1862
1863
1864
1865
1866
1867
1868
1869
1870
1871
1872
1873
1874
1875
1876
1877
1878
1879
1880
1881
1882
1883
1884
1885
1886
1887
1888
1889
1890
1891
1892
1893
1894
1895
1896
1897
1898
1899
1900
1901
1902
1903
1904
1905
1906
1907
1908
1909
1910
1911
1912
1913
1914
1915
1916
1917
1918
1919
1920
1921
1922
1923
1924
1925
1926
1927
1928
1929
1930
1931
1932
1933
1934
1935
1936
1937
1938
1939
1940
1941
1942
1943
1944
1945
1946
1947
1948
1949
1950
1951
1952
1953
1954
1955
1956
1957
1958
1959
1960
1961
1962
1963
1964
1965
1966
1967
1968
1969
1970
1971
1972
1973
1974
1975
1976
1977
1978
1979
1980
1981
1982
1983
1984
1985
1986
1987
1988
1989
1990
1991
1992
1993
1994
1995
1996
1997
1998
1999
2000
2001
2002
2003
	<?php
/* Licensed to the Apache Software Foundation (ASF) under one
// or more contributor license agreements.  See the NOTICE file
// distributed with this work for additional information
// regarding copyright ownership.  The ASF licenses this file
// to you under the Apache License, Version 2.0 (the
// "License"); you may not use this file except in compliance
// with the License.  You may obtain a copy of the License at

//   http://www.apache.org/licenses/LICENSE-2.0

// Unless required by applicable law or agreed to in writing,
// software distributed under the License is distributed on an
// "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
// KIND, either express or implied.  See the License for the
// specific language governing permissions and limitations
// under the License.
*/

require(dirname(__FILE__) . '/gcategories.class.php');

class GShoppingFlux extends Module
{
    private $_html = '';
    private $user_groups;
    
    const CHARSET = 'UTF-8';
    const REPLACE_FLAGS = ENT_COMPAT;
    
    function __construct()
    {
        $this->name    = 'gshoppingflux';
        $this->tab     = 'smart_shopping';
        $this->version = '1.5.6';
        $this->author  = 'Dim00z';
        
        $this->bootstrap = true;
        parent::__construct();
        
        $this->page        = basename(__FILE__, '.php');
        $this->displayName = $this->l('Google Shopping Flux');
        $this->description = $this->l('Export your products to Google Merchant Center, easily.');
        
        $this->need_instance          = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.5',
            'max' => '1.7'
        );
        
        $this->uri = ToolsCore::getCurrentUrlProtocolPrefix() . $this->context->shop->domain_ssl . $this->context->shop->physical_uri;
        
        $this->categories_values = array();
    }
    
    public function install($delete_params = true)
    {
        if (!parent::install() || !$this->registerHook('actionObjectCategoryAddAfter') || !$this->registerHook('actionObjectCategoryDeleteAfter') || !$this->registerHook('actionShopDataDuplication') || !$this->installDb()) {
            return false;
        }
        
        $shops   = Shop::getShops(true, null, true);
        $id_lang = (int) Context::getContext()->language->id;
        
        foreach ($shops as $shop_id) {
            $shop_group_id = Shop::getGroupFromShop($shop_id);
            
            if (!$this->initDb((int) $shop_id))
                return false;
            
            if ($delete_params)
                if (!Configuration::updateValue('GS_PRODUCT_TYPE', '', true, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_DESCRIPTION', 'short', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_SHIPPING_PRICE', '0.00', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_SHIPPING_COUNTRY', 'UK', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_IMG_TYPE', 'large_default', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_MPN_TYPE', 'reference', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_GENDER', '', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_AGE_GROUP', '', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_ATTRIBUTES', '0', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_COLOR', '', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_MATERIAL', '', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_PATTERN', '', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_SIZE', '', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_EXPORT_MIN_PRICE', '0.00', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_NO_GTIN', '1', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_NO_BRAND', '1', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_ID_EXISTS_TAG', '1', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_EXPORT_NAP', '0', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_QUANTITY', '1', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_FEATURED_PRODUCTS', '1', false, (int) $shop_group_id, (int) $shop_id) || !Configuration::updateValue('GS_GEN_FILE_IN_ROOT', '1', false, (int) $shop_group_id, (int) $shop_id))
                    return false;
            
        }
        
        // Get generation file route
        if (!is_dir(dirname(__FILE__) . '/file_exports'))
            @mkdir(dirname(__FILE__) . '/file_exports', 0755, true);
        
        @chmod(dirname(__FILE__) . '/file_exports', 0755);
        
        return true;
    }
    
    public function installDb()
    {
        return (Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gshoppingflux` (
            `id_gcategory` INT(11) UNSIGNED NOT NULL,
            `export` INT(11) UNSIGNED NOT NULL,
            `condition` VARCHAR( 12 ) NOT NULL,
            `availability` VARCHAR( 12 ) NOT NULL,
            `gender` VARCHAR( 8 ) NOT NULL,
            `age_group` VARCHAR( 8 ) NOT NULL,
            `color` VARCHAR( 64 ) NOT NULL,
            `material` VARCHAR( 64 ) NOT NULL,
            `pattern` VARCHAR( 64 ) NOT NULL,
            `size` VARCHAR( 64 ) NOT NULL,
            `id_shop` INT(11) UNSIGNED NOT NULL,
            INDEX (`id_shop`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8 COLLATE utf8_general_ci;') && Db::getInstance()->execute('
             CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gshoppingflux_lang` (
            `id_gcategory` INT(11) UNSIGNED NOT NULL,
            `id_lang` INT(11) UNSIGNED NOT NULL,
            `id_shop` INT(11) UNSIGNED NOT NULL,
            `gcategory` VARCHAR( 255 ) NOT NULL,
            INDEX ( `id_gcategory` , `id_lang`, `id_shop`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8 COLLATE utf8_general_ci;'));
    }
    
    public function initDb($id_shop)
    {
        $languages = $this->context->controller->getLanguages();
        $id_lang   = (int) Context::getContext()->language->id;
        $shops     = Shop::getShops(true, null, true);
        $str       = array();
        
        $shop = new Shop($id_shop);
        $root = Category::getRootCategory($id_lang, $shop);
        
        $categs = Db::getInstance()->executeS('
            SELECT c.id_category, c.id_parent, c.active
            FROM ' . _DB_PREFIX_ . 'category c
            INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cs.id_category=c.id_category AND cs.id_shop=' . (int) $id_shop . ')
            ORDER BY c.id_category ASC, c.level_depth ASC, cs.position ASC;');
        
        foreach ($categs as $kc => $cat) {
            foreach ($languages as $key => $lang)
                $str[$lang['id_lang']] = '';
            $condition    = '';
            $availability = '';
            $gender       = '';
            $age_group    = '';
            $color        = '';
            $material     = '';
            $pattern      = '';
            $size         = '';
            
            if (!count(GCategories::get($cat['id_category'], $id_lang, $id_shop)) && ($cat['id_category'] > 0)) {
                if ($root->id_category == $cat['id_category']) {
                    foreach ($languages as $key => $lang)
                        $str[$lang['id_lang']] = $this->l('Google Category Example > Google Sub-Category Example');
                    $condition    = 'new';
                    $availability = 'in stock';
                }
                GCategories::add($cat['id_category'], $str, $cat['active'], $condition, $availability, $gender, $age_group, $color, $material, $pattern, $size, $id_shop);
            }
        }
        return true;
    }
    
    public function uninstall($delete_params = true)
    {
        if (!parent::uninstall())
            return false;
        
        if ($delete_params)
            if (!$this->uninstallDB() || !Configuration::deleteByName('GS_PRODUCT_TYPE') || !Configuration::deleteByName('GS_DESCRIPTION') || !Configuration::deleteByName('GS_SHIPPING_PRICE') || !Configuration::deleteByName('GS_SHIPPING_COUNTRY') || !Configuration::deleteByName('GS_IMG_TYPE') || !Configuration::deleteByName('GS_MPN_TYPE') || !Configuration::deleteByName('GS_GENDER') || !Configuration::deleteByName('GS_AGE_GROUP') || !Configuration::deleteByName('GS_ATTRIBUTES') || !Configuration::deleteByName('GS_COLOR') || !Configuration::deleteByName('GS_MATERIAL') || !Configuration::deleteByName('GS_PATTERN') || !Configuration::deleteByName('GS_SIZE') || !Configuration::deleteByName('GS_EXPORT_MIN_PRICE') || !Configuration::deleteByName('GS_NO_GTIN') || !Configuration::deleteByName('GS_NO_BRAND') || !Configuration::deleteByName('GS_ID_EXISTS_TAG') || !Configuration::deleteByName('GS_EXPORT_NAP') || !Configuration::deleteByName('GS_QUANTITY') || !Configuration::deleteByName('GS_FEATURED_PRODUCTS') || !Configuration::deleteByName('GS_GEN_FILE_IN_ROOT'))
                return false;
        
        return true;
    }
    
    private function uninstallDb()
    {
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'gshoppingflux`');
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'gshoppingflux_lang`');
        return true;
    }
    
    public function reset()
    {
        if (!$this->uninstall(false))
            return false;
        if (!$this->install(false))
            return false;
        
        return true;
    }
    
    public function hookActionObjectCategoryAddAfter($params)
    {
        $shops = Shop::getShops(true, null, true);
        foreach ($shops as $id_shop)
            $this->initDb($id_shop);
    }
    
    public function hookActionObjectCategoryDeleteAfter($params)
    {
        $shops = Shop::getShops(true, null, true);
        foreach ($shops as $id_shop)
            $this->initDb($id_shop);
    }
    
    public function hookActionShopDataDuplication($params)
    {
        $gcategories = Db::getInstance()->executeS('
            SELECT *
            FROM ' . _DB_PREFIX_ . 'gshoppingflux
            WHERE id_shop = ' . (int) $params['old_id_shop']);
        
        foreach ($gcategories as $id => $gcateg) {
            Db::getInstance()->execute('
                INSERT IGNORE INTO ' . _DB_PREFIX_ . 'gshoppingflux (id_gcategory, id_shop)
                VALUES (null, ' . (int) $params['new_id_shop'] . ')');
            
            $gcategories[$id]['new_id_gcategory'] = Db::getInstance()->Insert_ID();
        }
        
        foreach ($gcategories as $id => $gcateg) {
            $lang = Db::getInstance()->executeS('
                    SELECT id_lang, ' . (int) $params['new_id_shop'] . ', gcategory
                    FROM ' . _DB_PREFIX_ . 'gshoppingflux_lang
                    WHERE id_gcategory = ' . (int) $gcateg['id_gcategory'] . ' AND id_shop = ' . (int) $params['old_id_shop']);
            
            foreach ($lang as $l)
                Db::getInstance()->execute('
                    INSERT IGNORE INTO ' . _DB_PREFIX_ . 'gshoppingflux_lang (id_gcategory, id_lang, id_shop, gcategory)
                    VALUES (' . (int) $gcateg['new_id_gcategory'] . ', ' . (int) $l['id_lang'] . ', ' . (int) $params['new_id_shop'] . ', ' . (int) $l['gcategory'] . ')');
        }
    }
    
    public function getContent()
    {
        $id_lang          = (int) Context::getContext()->language->id;
        $languages        = $this->context->controller->getLanguages();
        $default_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $shops            = Shop::getShops(true, null, true);
        $shop_id          = Shop::getContextShopID();
        $shop_group_id    = Shop::getGroupFromShop($shop_id);
        
        $gcategories = Tools::getValue('gcategory') ? array_filter(Tools::getValue('gcategory'), 'strlen') : array();
        
        if (count($shops) > 1 && !isset($shop_id)) {
            $this->_html .= $this->getWarningMultishopHtml();
            return $this->_html;
        }
        
        if (Shop::isFeatureActive())
            $this->_html .= $this->getCurrentShopInfoMsg();
        
        if (Tools::isSubmit('submitFluxOptions')) {
            $errors_update_shops = array();
            $updated             = true;
            $product_type_lang   = Tools::getValue('product_type');
            foreach ($languages as $k => $lang)
                $product_type[$lang['id_lang']] = $product_type_lang[$k];
            
            $updated &= Configuration::updateValue('GS_PRODUCT_TYPE', $product_type, false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_DESCRIPTION', Tools::getValue('description'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_SHIPPING_PRICE', (float) Tools::getValue('shipping_price'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_SHIPPING_COUNTRY', Tools::getValue('shipping_country'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_IMG_TYPE', Tools::getValue('img_type'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_MPN_TYPE', Tools::getValue('mpn_type'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_GENDER', Tools::getValue('gender'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_AGE_GROUP', Tools::getValue('age_group'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_ATTRIBUTES', Tools::getValue('export_attributes'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_COLOR', implode(';', Tools::getValue('color')), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_MATERIAL', implode(';', Tools::getValue('material')), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_PATTERN', implode(';', Tools::getValue('pattern')), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_SIZE', implode(';', Tools::getValue('size')), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_EXPORT_MIN_PRICE', (float) Tools::getValue('export_min_price'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_NO_GTIN', (bool) Tools::getValue('no_gtin'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_NO_BRAND', (bool) Tools::getValue('no_brand'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_ID_EXISTS_TAG', (bool) Tools::getValue('id_exists_tag'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_EXPORT_NAP', (bool) Tools::getValue('export_nap'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_QUANTITY', (bool) Tools::getValue('quantity'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_FEATURED_PRODUCTS', (bool) Tools::getValue('featured_products'), false, (int) $shop_group_id, (int) $shop_id);
            $updated &= Configuration::updateValue('GS_GEN_FILE_IN_ROOT', (bool) Tools::getValue('gen_file_in_root'), false, (int) $shop_group_id, (int) $shop_id);
            
            if (!$updated) {
                $shop                  = new Shop($shop_id);
                $errors_update_shops[] = $shop->name;
            }
            
            $count     = $this->generateShopFileList((int) $shop_id);
            // Get active langs on shop
            $languages = Language::getLanguages();
            
            $confirm = '';
            if (!count($errors_update_shops)) {
                $confirm = $this->l('The settings have been updated.');
                foreach ($languages as $i => $lang) {
                    if (Configuration::get('GS_GEN_FILE_IN_ROOT', 0, $shop_group_id, $shop_id) == 1) {
                        $get_file_url = $this->uri . $this->_getOutputFileName($lang['iso_code'], $shop_id);
                    } else {
                        $get_file_url = $this->uri . 'modules/' . $this->name . '/file_exports/' . $this->_getOutputFileName($lang['iso_code'], $shop_id);
                    }
                    $confirm .= '<br /> <a href="' . $get_file_url . '" target="_blank">' . $get_file_url . '</a> : ' . ($count[$i]['nb_products'] - $count[$i]['nb_combinations']) . ' ' . $this->l('products exported');
                    if ($count[$i]['nb_combinations'] > 0) {
                        $confirm .= ': ' . $count[$i]['nb_prod_w_attr'] . ' ' . $this->l('products with attributes');
                        $confirm .= ', ' . $count[$i]['nb_combinations'] . ' ' . $this->l('attributes combinations');
                        $confirm .= '.<br/> ' . $this->l('Total') . ': ' . ($count[$i]['nb_products']) . ' ' . $this->l('exported products');
                        if ($count[$i]['non_exported_products'] > 0)
                            $confirm .= ', ' . $this->l('and') . ' ' . $count[$i]['non_exported_products'] . ' ' . $this->l('not-exported products (non-available)');
                        $confirm .= '.';
                        
                    } else
                        $confirm .= '.';
                }
                $this->_html .= $this->displayConfirmation(html_entity_decode($confirm));
            } else {
                $this->_html .= $this->displayError(sprintf($this->l('Unable to update settings for the following shop: %s'), implode(', ', $errors_update_shops)));
            }
            
        } elseif (Tools::isSubmit('updateCategory')) {
            $id_gcategory = (int) Tools::getValue('id_gcategory', 0);
            $export       = (int) Tools::getValue('export_active_on', 0);
            $condition    = Tools::getValue('condition');
            $availability = Tools::getValue('availability');
            $gender       = Tools::getValue('gender');
            $age_group    = Tools::getValue('age_group');
            $color        = implode(";", Tools::getValue('color'));
            $material     = implode(";", Tools::getValue('material'));
            $pattern      = implode(";", Tools::getValue('pattern'));
            $size         = implode(";", Tools::getValue('size'));
            $id_shop      = (int) Shop::getContextShopID();
            
            if (Tools::isSubmit('updatecateg')) {
                $gcateg = array();
                
                foreach (Language::getLanguages(false) as $lang) {
                    $gcateg[$lang['id_lang']] = Tools::getValue('gcategory_' . (int) $lang['id_lang']);
                }
                
                GCategories::update($id_gcategory, $gcateg, $export, $condition, $availability, $gender, $age_group, $color, $material, $pattern, $size, $id_shop);
                $this->_html .= $this->displayConfirmation($this->l('Google category has been updated.'));
            }
        }
        
        if ((Tools::getIsset('updategshoppingflux') || Tools::getIsset('statusgshoppingflux')) && !Tools::getValue('updategshoppingflux'))
            $this->_html .= $this->renderAddForm();
        
        $this->_html .= $this->renderForm();
        
        $gcategories = GCategories::gets((int) $id_lang, null, (int) $shop_id);
        
        if (!count($gcategories))
            return $this->_html;
        
        $this->_html .= $this->renderList();
        $this->_html .= $this->renderInfo();
        return $this->_html;
    }
    
    private function getWarningMultishopHtml()
    {
        return '<p class="alert alert-warning">' . $this->l('You cannot manage Google categories from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit') . '</p>';
    }
    
    private function getCurrentShopInfoMsg()
    {
        $shop_info = null;
        
        if (Shop::getContext() == Shop::CONTEXT_SHOP)
            $shop_info = sprintf($this->l('The modifications will be applied to shop: %s'), $this->context->shop->name);
        else if (Shop::getContext() == Shop::CONTEXT_GROUP)
            $shop_info = sprintf($this->l('The modifications will be applied to this group: %s'), Shop::getContextShopGroup()->name);
        else
            $shop_info = $this->l('The modifications will be applied to all shops');
        
        return '<div class="alert alert-info">' . $shop_info . '</div>';
    }
    
    public function getShopFeatures($id_lang, $id_shop)
    {
        $sql = 'SELECT fl.* FROM ' . _DB_PREFIX_ . 'feature f ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = f.id_feature) ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'feature_shop fs ON (fs.id_feature = f.id_feature) ' . 'WHERE fl.id_lang = ' . (int) $id_lang . ' AND fs.id_shop = ' . (int) $id_shop . ' ' . 'ORDER BY f.id_feature ASC;';
        $ret = Db::getInstance()->executeS($sql);
        return ($ret);
    }
    
    public function getShopAttributes($id_lang, $id_shop)
    {
        $sql = 'SELECT agl.* FROM ' . _DB_PREFIX_ . 'attribute_group_lang agl ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_group_shop ags ON (ags.id_attribute_group = agl.id_attribute_group) ' . 'WHERE agl.id_lang = ' . (int) $id_lang . ' AND ags.id_shop = ' . (int) $id_shop . ' ' . 'ORDER BY ags.id_attribute_group ASC;';
        $ret = Db::getInstance()->executeS($sql);
        return ($ret);
    }
    
    public function getProductFeatures($id_product, $id_lang, $id_shop)
    {
        $sql = 'SELECT fl.*, fv.value FROM ' . _DB_PREFIX_ . 'feature_product fp ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = fp.id_feature) ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'feature_shop fs ON (fs.id_feature = fp.id_feature) ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fv ON (fv.id_feature_value = fp.id_feature_value AND fv.id_lang = fl.id_lang) ' . 'WHERE fp.id_product = ' . (int) $id_product . ' AND fl.id_lang = ' . (int) $id_lang . ' AND fs.id_shop = ' . (int) $id_shop . ' ' . 'ORDER BY fp.id_feature ASC;';
        $ret = Db::getInstance()->executeS($sql);
        return ($ret);
    }
    
    public function renderForm()
    {
        
        $helper                           = new HelperForm();
        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $lang                             = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language    = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form                = array();
        $helper->module                   = $this;
        $helper->identifier               = $this->identifier;
        $helper->currentIndex             = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token                    = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars                 = array(
            'fields_value' => $this->getConfigFieldsValues($this->context->shop->id),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        $shops                            = Shop::getShops(true, null, true);
        $context                          = Context::getContext();
        $id_lang                          = $context->language->id;
        $id_shop                          = $context->shop->id;
        $img_types                        = ImageType::getImagesTypes('products');
        
        $features     = array(
            array(
                'id_feature' => '',
                'name' => $this->l('Product feature doesn\'t exist')
            )
        );
        $features     = array_merge($features, $this->getShopFeatures($id_lang, $id_shop));
        $descriptions = array(
            array(
                'id_desc' => 'short',
                'name' => $this->l('Short description')
            ),
            array(
                'id_desc' => 'long',
                'name' => $this->l('Long description')
            ),
            array(
                'id_desc' => 'meta',
                'name' => $this->l('Meta description')
            )
        );
        $mpn_types    = array(
            array(
                'id_mpn' => 'reference',
                'name' => $this->l('Reference')
            ),
            array(
                'id_mpn' => 'supplier_reference',
                'name' => $this->l('Supplier reference')
            )
        );
        $form_desc    = html_entity_decode($this->l('Please visit and read the <a href="http://support.google.com/merchants/answer/188494" target="_blank">Google Shopping Products Feed Specification</a> if you don\'t know how to configure these options. <br/> If all your shop products match the same Google Shopping category, you can attach it to your home category in the table below, sub-categories will automatically get the same setting. No need to fill each Google category field. <br/> Products in categories with no Google category specified are exported in the Google Shopping category linked to the nearest parent.'));
        
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Parameters'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Default product type'),
                        'name' => 'product_type[]',
                        //'class' => 'fixed-width-xl',
                        'lang' => true,
                        'desc' => $this->l('Default product type from your shop.')
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Description type'),
                        'name' => 'description',
                        'default_value' => $helper->tpl_vars['fields_value']['description'],
                        'options' => array(
                            //'default' => array('value' => 0, 'label' => $this->l('Choose description type')),
                            'query' => $descriptions,
                            'id' => 'id_desc',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Shipping price'),
                        'name' => 'shipping_price',
                        'class' => 'fixed-width-xs',
                        'prefix' => $this->context->currency->sign
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Shipping country'),
                        'name' => 'shipping_country',
                        'class' => 'fixed-width-xs'
                        //'suffix' => strtoupper($this->context->language->iso_code),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Images type'),
                        'name' => 'img_type',
                        'default_value' => $helper->tpl_vars['fields_value']['img_type'],
                        'options' => array(
                            //'default' => array('value' => 0, 'label' => $this->l('Choose image type')),
                            'query' => $img_types,
                            'id' => 'name',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Manufacturers References type (MPN)'),
                        'name' => 'mpn_type',
                        'default_value' => $helper->tpl_vars['fields_value']['mpn_type'],
                        'options' => array(
                            'query' => $mpn_types,
                            'id' => 'id_mpn',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimum product price'),
                        'name' => 'export_min_price',
                        'class' => 'fixed-width-xs',
                        'prefix' => $this->context->currency->sign,
                        'desc' => $this->l('Products at lower price are not exported. Enter 0.00 for no use.'),
                        'required' => true
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Products gender feature'),
                        'name' => 'gender',
                        'default_value' => $helper->tpl_vars['fields_value']['gender'],
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Products age group feature'),
                        'name' => 'age_group',
                        'default_value' => $helper->tpl_vars['fields_value']['age_group'],
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Products color feature'),
                        'name' => 'color[]',
                        'default_value' => $helper->tpl_vars['fields_value']['color'],
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Hold [Ctrl] key pressed to select multiple color features.')
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Products material feature'),
                        'name' => 'material[]',
                        'default_value' => $helper->tpl_vars['fields_value']['material'],
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Hold [Ctrl] key pressed to select multiple material features.')
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Products pattern feature'),
                        'name' => 'pattern[]',
                        'default_value' => $helper->tpl_vars['fields_value']['pattern'],
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Hold [Ctrl] key pressed to select multiple pattern features.')
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Products size feature'),
                        'name' => 'size[]',
                        'default_value' => $helper->tpl_vars['fields_value']['size'],
                        'options' => array(
                            'query' => $features,
                            'id' => 'id_feature',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Hold [Ctrl] key pressed to select multiple size features.')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Export attributes combinations'),
                        'name' => 'export_attributes',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('If checked, one product is exported for each attributes combination. Products should have at least one attribute filled in order to be exported as combinations.')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Export products with no GTIN code'),
                        'name' => 'no_gtin',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Export products with no brand'),
                        'name' => 'no_brand',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Set <identifier_exists> tag to FALSE'),
                        'name' => 'id_exists_tag',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Export non-available products'),
                        'name' => 'export_nap',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Export product quantity'),
                        'name' => 'quantity',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Export "On Sale" indication'),
                        'name' => 'featured_products',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Generate the files to the root of the site'),
                        'name' => 'gen_file_in_root',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    )
                ),
                'description' => $form_desc,
                'submit' => array(
                    'name' => 'submitFluxOptions',
                    'title' => $this->l('Save & Export')
                )
            )
        );
        
        return $helper->generateForm(array(
            $fields_form
        ));
    }
    
    public function getConfigFieldsValues($shop_id)
    {
        $shop_group_id     = Shop::getGroupFromShop($shop_id);
        $product_type      = array();
        $description       = 'short';
        $shipping_price    = 0;
        $shipping_country  = 'UK';
        $img_type          = 'large_default';
        $mpn_type          = '';
        $gender            = '';
        $age_group         = '';
        $export_attributes = '';
        $color             = array();
        $material          = array();
        $pattern           = array();
        $size              = array();
        $export_min_price  = 0;
        $no_gtin           = true;
        $no_brand          = true;
        $id_exists_tag     = true;
        $export_nap        = true;
        $quantity          = true;
        $featured_products = true;
        $gen_file_in_root  = true;
        
        foreach (Language::getLanguages(false) as $lang)
            $product_type[$lang['id_lang']] = Configuration::get('GS_PRODUCT_TYPE', $lang['id_lang'], $shop_group_id, $shop_id);
        
        $description       = Configuration::get('GS_DESCRIPTION', 0, $shop_group_id, $shop_id);
        $shipping_price    = (float) Configuration::get('GS_SHIPPING_PRICE', 0, $shop_group_id, $shop_id);
        $shipping_country  = Configuration::get('GS_SHIPPING_COUNTRY', 0, $shop_group_id, $shop_id);
        $img_type          = Configuration::get('GS_IMG_TYPE', 0, $shop_group_id, $shop_id);
        $mpn_type          = Configuration::get('GS_MPN_TYPE', 0, $shop_group_id, $shop_id);
        $gender            = Configuration::get('GS_GENDER', 0, $shop_group_id, $shop_id);
        $age_group         = Configuration::get('GS_AGE_GROUP', 0, $shop_group_id, $shop_id);
        $export_attributes = Configuration::get('GS_ATTRIBUTES', 0, $shop_group_id, $shop_id);
        $color             = explode(";", Configuration::get('GS_COLOR', 0, $shop_group_id, $shop_id));
        $material          = explode(";", Configuration::get('GS_MATERIAL', 0, $shop_group_id, $shop_id));
        $pattern           = explode(";", Configuration::get('GS_PATTERN', 0, $shop_group_id, $shop_id));
        $size              = explode(";", Configuration::get('GS_SIZE', 0, $shop_group_id, $shop_id));
        $export_min_price  = (float) Configuration::get('GS_EXPORT_MIN_PRICE', 0, $shop_group_id, $shop_id);
        $no_gtin &= (bool) Configuration::get('GS_NO_GTIN', 0, $shop_group_id, $shop_id);
        $no_brand &= (bool) Configuration::get('GS_NO_BRAND', 0, $shop_group_id, $shop_id);
        $id_exists_tag &= (bool) Configuration::get('GS_ID_EXISTS_TAG', 0, $shop_group_id, $shop_id);
        $export_nap &= (bool) Configuration::get('GS_EXPORT_NAP', 0, $shop_group_id, $shop_id);
        $quantity &= (bool) Configuration::get('GS_QUANTITY', 0, $shop_group_id, $shop_id);
        $featured_products &= (bool) Configuration::get('GS_FEATURED_PRODUCTS', 0, $shop_group_id, $shop_id);
        $gen_file_in_root &= (bool) Configuration::get('GS_GEN_FILE_IN_ROOT', 0, $shop_group_id, $shop_id);
        
        return array(
            'product_type[]' => $product_type,
            'description' => $description,
            'shipping_price' => (float) $shipping_price,
            'shipping_country' => $shipping_country,
            'img_type' => $img_type,
            'mpn_type' => $mpn_type,
            'gender' => $gender,
            'age_group' => $age_group,
            'export_attributes' => (float) $export_attributes,
            'color[]' => $color,
            'material[]' => $material,
            'pattern[]' => $pattern,
            'size[]' => $size,
            'export_min_price' => (float) $export_min_price,
            'no_gtin' => (int) $no_gtin,
            'no_brand' => (int) $no_brand,
            'id_exists_tag' => (int) $id_exists_tag,
            'export_nap' => (int) $export_nap,
            'quantity' => (int) $quantity,
            'featured_products' => (int) $featured_products,
            'gen_file_in_root' => (int) $gen_file_in_root
        );
    }
    
    public function renderAddForm()
    {
        $helper                           = new HelperForm();
        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $helper->languages                = $this->context->controller->getLanguages();
        $helper->default_form_language    = (int) $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form                = array();
        $helper->fields_value             = $this->getGCategFieldsValues();
        $helper->identifier               = $this->identifier;
        $helper->currentIndex             = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token                    = Tools::getAdminTokenLite('AdminModules');
        
        $context = Context::getContext();
        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;
        
        $conditions   = array(
            array(
                'id_cond' => '',
                'name' => $this->l('Default')
            ),
            array(
                'id_cond' => 'new',
                'name' => $this->l('Category\'s products are new')
            ),
            array(
                'id_cond' => 'used',
                'name' => $this->l('Category\'s products are used')
            ),
            array(
                'id_cond' => 'refurbished',
                'name' => $this->l('Category\'s products are refurbished')
            )
        );
        $avail_modes  = array(
            array(
                'id_mode' => '',
                'name' => $this->l('Default')
            ),
            array(
                'id_mode' => 'in stock',
                'name' => $this->l('Category\'s products are in stock')
            ),
            array(
                'id_mode' => 'preorder',
                'name' => $this->l('Category\'s products avail. on preorder')
            )
        );
        $gender_modes = array(
            array(
                'id' => '',
                'name' => $this->l('Default')
            ),
            array(
                'id' => 'male',
                'name' => $this->l('Category\'s products are for men')
            ),
            array(
                'id' => 'female',
                'name' => $this->l('Category\'s products are for women')
            ),
            array(
                'id' => 'unisex',
                'name' => $this->l('Category\'s products are unisex')
            )
        );
        $age_modes    = array(
            array(
                'id' => '',
                'name' => $this->l('Default')
            ),
            array(
                'id' => 'newborn',
                'name' => $this->l('Newborn')
            ),
            array(
                'id' => 'infant',
                'name' => $this->l('Infant')
            ),
            array(
                'id' => 'toddler',
                'name' => $this->l('Toddler')
            ),
            array(
                'id' => 'kids',
                'name' => $this->l('Kids')
            ),
            array(
                'id' => 'adult',
                'name' => $this->l('Adult')
            )
        );
        $attributes   = array(
            array(
                'id_attribute_group' => '',
                'name' => $this->l('Products attribute doesn\'t exist')
            )
        );
        $attributes   = array_merge($attributes, $this->getShopAttributes($id_lang, $id_shop));
        
        $gcat_desc = '<a href="http://www.google.com/support/merchants/bin/answer.py?answer=160081&query=product_type" target="_blank">' . $this->l('See Google Categories') . '</a> ';
        
        $form_desc = html_entity_decode($this->l('Default: System tries to get the value of the product attribute. If not found, system tries to get the category\'s attribute value. <br> If not found, it tries to get the parent category\'s attribute, and so till the root category. At last, if empty, value is not exported.'));
        
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => ((Tools::getIsset('updategshoppingflux') || Tools::getIsset('statusgshoppingflux')) && !Tools::getValue('updategshoppingflux')) ? $this->l('Update the matching Google category') : $this->l('Add a new Google category'),
                    'icon' => 'icon-link'
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' => $this->l('Category'),
                        'name' => 'breadcrumb'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Matching Google category'),
                        'name' => 'gcategory',
                        'lang' => true,
                        'desc' => $gcat_desc
                    ),
                    array(
                        'type' => 'checkbox',
                        'name' => 'export',
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'active_on',
                                    'name' => $this->l('Export products from this category'),
                                    'val' => '1'
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Condition'),
                        'name' => 'condition',
                        'default_value' => $helper->fields_value['condition'],
                        'options' => array(
                            'query' => $conditions,
                            'id' => 'id_cond',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Products\' availability'),
                        'name' => 'availability',
                        'default_value' => $helper->fields_value['availability'],
                        'options' => array(
                            'query' => $avail_modes,
                            'id' => 'id_mode',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Gender attribute'),
                        'name' => 'gender',
                        'default_value' => $helper->fields_value['gender'],
                        'options' => array(
                            'query' => $gender_modes,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Age group'),
                        'name' => 'age_group',
                        'default_value' => $helper->fields_value['age_group'],
                        'options' => array(
                            'query' => $age_modes,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Products color attribute'),
                        'name' => 'color[]',
                        'default_value' => $helper->fields_value['color'],
                        'options' => array(
                            'query' => $attributes,
                            'id' => 'id_attribute_group',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Hold [Ctrl] key pressed to select multiple color attributes.')
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Products material attribute'),
                        'name' => 'material[]',
                        'default_value' => $helper->fields_value['material'],
                        'options' => array(
                            'query' => $attributes,
                            'id' => 'id_attribute_group',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Hold [Ctrl] key pressed to select multiple material attributes.')
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Products pattern attribute'),
                        'name' => 'pattern[]',
                        'default_value' => $helper->fields_value['pattern'],
                        'options' => array(
                            'query' => $attributes,
                            'id' => 'id_attribute_group',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Hold [Ctrl] key pressed to select multiple pattern attributes.')
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Products size attribute'),
                        'name' => 'size[]',
                        'default_value' => $helper->fields_value['size'],
                        'options' => array(
                            'query' => $attributes,
                            'id' => 'id_attribute_group',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Hold [Ctrl] key pressed to select multiple size attributes.')
                    )
                ),
                'description' => $form_desc,
                'submit' => array(
                    'name' => 'submitCategory',
                    'title' => $this->l('Save')
                )
            )
        );
        
        if ((Tools::getIsset('updategshoppingflux') || Tools::getIsset('statusgshoppingflux')) && !Tools::getValue('updategshoppingflux'))
            $fields_form['form']['submit'] = array(
                'name' => 'updateCategory',
                'title' => $this->l('Update')
            );
        
        if (Tools::isSubmit('updategshoppingflux') || Tools::isSubmit('statusgshoppingflux')) {
            $fields_form['form']['input'][]      = array(
                'type' => 'hidden',
                'name' => 'updatecateg'
            );
            $fields_form['form']['input'][]      = array(
                'type' => 'hidden',
                'name' => 'id_gcategory'
            );
            $helper->fields_value['updatecateg'] = '';
        }
        
        return $helper->generateForm(array(
            $fields_form
        ));
    }
    
    public function getGCategFieldsValues()
    {
        $gcatexport_active  = '';
        $gcatcondition_edit = '';
        $gcatavail_edit     = '';
        $gcatgender_edit    = '';
        $gcatage_edit       = '';
        $gcatcolor_edit     = '';
        $gcatmaterial_edit  = '';
        $gcatpattern_edit   = '';
        $gcatsize_edit      = '';
        $gcategory_edit     = '';
        $gcatlabel_edit     = '';
        
        if (Tools::isSubmit('updategshoppingflux') || Tools::isSubmit('statusgshoppingflux')) {
            $gcateg = GCategories::getCategLang(Tools::getValue('id_gcategory'), (int) Shop::getContextShopID());
            
            foreach ($gcateg['gcategory'] as $key => $categ)
                $gcateg['gcategory'][$key] = Tools::htmlentitiesDecodeUTF8($categ);
            
            $gcatexport_active  = $gcateg['export'];
            $gcatcondition_edit = $gcateg['condition'];
            $gcatavail_edit     = $gcateg['availability'];
            $gcatgender_edit    = $gcateg['gender'];
            $gcatage_edit       = $gcateg['age_group'];
            $gcatcolor_edit     = $gcateg['color'];
            $gcatmaterial_edit  = $gcateg['material'];
            $gcatpattern_edit   = $gcateg['pattern'];
            $gcatsize_edit      = $gcateg['size'];
            $gcategory_edit     = $gcateg['gcategory'];
            $gcatlabel_edit     = $gcateg['breadcrumb'];
            
        }
        
        $fields_values = array(
            'id_gcategory' => Tools::getValue('id_gcategory'),
            'breadcrumb' => (isset($gcatlabel_edit) ? $gcatlabel_edit : ''),
            'export_active_on' => Tools::getValue('export_active_on', isset($gcatexport_active) ? $gcatexport_active : ''),
            'condition' => Tools::getValue('condition', isset($gcatcondition_edit) ? $gcatcondition_edit : ''),
            'availability' => Tools::getValue('availability', isset($gcatavail_edit) ? $gcatavail_edit : ''),
            'gender' => Tools::getValue('gender', isset($gcatgender_edit) ? $gcatgender_edit : ''),
            'age_group' => Tools::getValue('age_group', isset($gcatage_edit) ? $gcatage_edit : ''),
            'color[]' => explode(';', Tools::getValue('color', isset($gcatcolor_edit) ? $gcatcolor_edit : '')),
            'material[]' => explode(';', Tools::getValue('material', isset($gcatmaterial_edit) ? $gcatmaterial_edit : '')),
            'pattern[]' => explode(';', Tools::getValue('pattern', isset($gcatpattern_edit) ? $gcatpattern_edit : '')),
            'size[]' => explode(';', Tools::getValue('size', isset($gcatsize_edit) ? $gcatsize_edit : ''))
            
        );
        
        if (Tools::getValue('submitAddmodule')) {
            foreach (Language::getLanguages(false) as $lang) {
                $fields_values['gcategory'][$lang['id_lang']] = '';
            }
        } else
            foreach (Language::getLanguages(false) as $lang) {
                $fields_values['gcategory'][$lang['id_lang']] = Tools::getValue('gcategory_' . (int) $lang['id_lang'], isset($gcategory_edit[$lang['id_lang']]) ? html_entity_decode($gcategory_edit[$lang['id_lang']]) : '');
            }
        
        return $fields_values;
    }
    
    public function renderList()
    {
        $gcategories = $this->makeCatTree();
        $fields_list = array(
            'id_gcategory' => array(
                'title' => $this->l('Category ID')
            )
        );
        
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(true, null, true)) > 1)
            $fields_list = array_merge($fields_list, array(
                'shop_name' => array(
                    'title' => $this->l('Shop name'),
                    'width' => '15%'
                )
            ));
        
        $form_desc = "If all your products match the same Google category, you can attach it to your home category, sub-categories will automatically get the same setting. Products in categories with no Google category specified are exported in the same place than the one from parent categories.";
        
        $fields_list = array_merge($fields_list, array(
            'gcat_name' => array(
                'title' => $this->l('Category'),
                'width' => '30%'
            ),
            'gcategory' => array(
                'title' => $this->l('Matching Google category'),
                'width' => '70%'
            ),
            'condition' => array(
                'title' => $this->l('Condit.')
            ),
            'availability' => array(
                'title' => $this->l('Avail.')
            ),
            'gender' => array(
                'title' => $this->l('Gender')
            ),
            'age_group' => array(
                'title' => $this->l('Age')
            ),
            'gid_colors' => array(
                'title' => $this->l('Color')
            ),
            'gid_materials' => array(
                'title' => $this->l('Material')
            ),
            'gid_patterns' => array(
                'title' => $this->l('Pattern')
            ),
            'gid_sizes' => array(
                'title' => $this->l('Size')
            ),
            'export' => array(
                'title' => $this->l('Export'),
                'align' => 'center',
                'is_bool' => true,
                'active' => 'status'
            )
        ));
        
        $helper                = new HelperList();
        $helper->shopLinkType  = '';
        $helper->simple_header = true;
        $helper->identifier    = 'id_gcategory';
        $helper->table         = 'gshoppingflux';
        $helper->actions       = array(
            'edit'
        );
        $helper->show_toolbar  = false;
        $helper->module        = $this;
        $helper->title         = $this->l('Google categories');
        $helper->token         = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex  = AdminController::$currentIndex . '&configure=' . $this->name;
        
        return $helper->generateList($gcategories, $fields_list);
    }
    
    public function renderInfo()
    {
        $helper                           = new HelperForm();
        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $helper->languages                = $this->context->controller->getLanguages();
        $helper->default_form_language    = (int) $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form                = array();
        $helper->identifier               = $this->identifier;
        $helper->currentIndex             = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token                    = Tools::getAdminTokenLite('AdminModules');
        
        $context = Context::getContext();
        
        // Get active langs on shop
        $languages = Language::getLanguages();
        $shops     = Shop::getShops(true, null, true);
        $output    = '';
        
        foreach ($languages as $i => $lang) {
            if (Configuration::get('GS_GEN_FILE_IN_ROOT') == 1) {
                $get_file_url = $this->uri . $this->_getOutputFileName($lang['iso_code'], $context->shop->id);
            }
            
            else {
                $get_file_url = $this->uri . 'modules/' . $this->name . '/file_exports/' . $this->_getOutputFileName($lang['iso_code'], $context->shop->id);
            }
            
            $output .= '<a href="' . $get_file_url . '">' . $get_file_url . '</a> <br /> ';
        }
        $info_cron = '<a href="' . $this->uri . 'modules/' . $this->name . '/cron.php" target="_blank">' . $this->uri . 'modules/' . $this->name . '/cron.php</a>';
        if (count($languages) > 1)
            $files_desc = $this->l('Configure these URLs in your Google Merchant Center account.');
        else
            $files_desc = $this->l('Configure this URL in your Google Merchant Center account.');
        $cron_desc = $this->l('Install a CRON task to update the feed frequently.');
        
        if (count($shops) > 1)
            $cron_desc .= ' ' . $this->l('Please note that as multishop feature is active, you\'ll have to install several CRON tasks, one for each shop.');
        
        $form_desc            = $this->l('Report bugs and find help on forum: <a href="http://www.prestashop.com/forums/topic/381026-free-module-google-shopping-flux/" target="_blank">http://www.prestashop.com/forums/topic/381026-free-module-google-shopping-flux/</a>');
        $helper->fields_value = array(
            'info_files' => $output,
            'info_cron' => $info_cron
        );
        
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Files information'),
                    'icon' => 'icon-info'
                ),
                'input' => array(
                    array(
                        'type' => 'free',
                        'label' => $this->l('Generated files links:'),
                        'name' => 'info_files',
                        'desc' => $files_desc
                    ),
                    array(
                        'type' => 'free',
                        'label' => $this->l('Automatic files generation:'),
                        'name' => 'info_cron',
                        'desc' => $cron_desc
                    )
                ),
                'description' => html_entity_decode($form_desc, self::REPLACE_FLAGS, self::CHARSET)
            )
        );
        
        return $helper->generateForm(array(
            $fields_form
        ));
    }
    
    public function customGetNestedCategories($shop_id, $root_category = null, $id_lang = false, $active = true, $groups = null, $use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
    {
        if (isset($root_category) && !Validate::isInt($root_category))
            die(Tools::displayError());
        
        if (!Validate::isBool($active))
            die(Tools::displayError());
        
        if (isset($groups) && Group::isFeatureActive() && !is_array($groups))
            $groups = (array) $groups;
        
        $cache_id = 'Category::getNestedCategories_' . md5((int) $shop_id . (int) $root_category . (int) $id_lang . (int) $active . (int) $active . (isset($groups) && Group::isFeatureActive() ? implode('', $groups) : ''));
        
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('
                SELECT c.*, cl.`name` as gcat_name, g.*, gl.*, s.name as shop_name
                FROM `' . _DB_PREFIX_ . 'category` c
                INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = "' . (int) $shop_id . '")
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category` AND cl.`id_shop` = "' . (int) $shop_id . '"
                LEFT JOIN `' . _DB_PREFIX_ . 'gshoppingflux` g ON g.`id_gcategory` = c.`id_category` AND g.`id_shop` = "' . (int) $shop_id . '"
                LEFT JOIN `' . _DB_PREFIX_ . 'gshoppingflux_lang` gl ON gl.`id_gcategory` = c.`id_category` AND gl.`id_shop` = "' . (int) $shop_id . '"
                LEFT JOIN ' . _DB_PREFIX_ . 'shop s ON s.`id_shop` = "' . (int) $shop_id . '"
                WHERE 1 ' . $sql_filter . ' ' . ($id_lang ? 'AND cl.`id_lang` = ' . (int) $id_lang . ' AND gl.`id_lang` = ' . (int) $id_lang : '') . '
                ' . ($active ? ' AND c.`active` = 1' : '') . '
                ' . (isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN (' . implode(',', $groups) . ')' : '') . '
                ' . (!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '') . '
                ' . ($sql_sort != '' ? $sql_sort : ' ORDER BY c.`level_depth` ASC') . '
                ' . ($sql_sort == '' && $use_shop_restriction ? ', cs.`position` ASC' : '') . '
                ' . ($sql_limit != '' ? $sql_limit : ''));
            
            $context    = Context::getContext();
            $id_lang    = $context->language->id;
            $id_shop    = $context->shop->id;
            $attributes = $this->getShopAttributes($id_lang, $id_shop);
            
            foreach ($result as $k => $cat) {
                $result[$k]['gcategory'] = html_entity_decode($result[$k]['gcategory']);
                $gid_colors              = array();
                $gid_materials           = array();
                $gid_patterns            = array();
                $gid_sizes               = array();
                
                if ($result[$k]['level_depth'] > 0) {
                    $tree = " > ";
                    $str  = '';
                    for ($i = 0; $i < $result[$k]['level_depth'] - 1; $i++) {
                        $str .= $tree;
                    }
                    
                    $result[$k]['gcat_name'] = $str . ' ' . $result[$k]['gcat_name'];
                    
                    if (Configuration::get('GS_ATTRIBUTES') == 1) {
                        $result[$k]['color'] = explode(";", $result[$k]['color']);
                        foreach ($result[$k]['color'] as $a => $v) {
                            $gid_colors[] = $attributes[$v - 1]['name'];
                        }
                        
                        $result[$k]['material'] = explode(";", $result[$k]['material']);
                        foreach ($result[$k]['material'] as $a => $v) {
                            $gid_materials[] = $attributes[$v - 1]['name'];
                        }
                        
                        $result[$k]['pattern'] = explode(";", $result[$k]['pattern']);
                        foreach ($result[$k]['pattern'] as $a => $v) {
                            $gid_patterns[] = $attributes[$v - 1]['name'];
                        }
                        
                        $result[$k]['size'] = explode(";", $result[$k]['size']);
                        foreach ($result[$k]['size'] as $a => $v) {
                            $gid_sizes[] = $attributes[$v - 1]['name'];
                        }
                        
                        $result[$k]['gid_colors']    = implode(" ; ", $gid_colors);
                        $result[$k]['gid_materials'] = implode(" ; ", $gid_materials);
                        $result[$k]['gid_patterns']  = implode(" ; ", $gid_patterns);
                        $result[$k]['gid_sizes']     = implode(" ; ", $gid_sizes);
                    }
                }
            }
            
            $categories = array();
            $buff       = array();
            
            if (!isset($root_category))
                $root_category = 1;
            
            foreach ($result as $row) {
                $current =& $buff[$row['id_category']];
                $current = $row;
                
                if ($row['id_category'] == $root_category)
                    $categories[$row['id_category']] =& $current;
                else
                    $buff[$row['id_parent']]['children'][$row['id_category']] =& $current;
            }
            
            Cache::store($cache_id, $categories);
        }
        
        return Cache::retrieve($cache_id);
    }
    
    private function makeCatTree($id_cat = 0, $catlist = 0)
    {
        $id_lang = (int) $this->context->language->id;
        $id_shop = (int) Shop::getContextShopID();
        
        if ($id_cat == 0 && $catlist == 0) {
            $catlist = array();
            $shop    = new Shop($id_shop);
            $id_cat  = Category::getRootCategory($id_lang, $shop);
            $id_cat  = $id_cat->id_category;
        }
        
        $category = new Category((int) $id_cat, (int) $id_lang);
        
        if (Validate::isLoadedObject($category)) {
            $tabcat  = $this->customGetNestedCategories($id_shop, $id_cat, $id_lang, true, $this->user_groups, true);
            $catlist = array_merge($catlist, $tabcat);
        }
        
        foreach ($tabcat as $k => $c) {
            if (!empty($c['children'])) {
                foreach ($c['children'] as $i => $j) {
                    $catlist = $this->makeCatTree($j['id_category'], $catlist);
                }
            }
        }
        
        return $catlist;
    }
    
    public function getGCategValues($id_lang, $id_shop)
    {
        // Get categories' export values, or it's parents ones :
        // Matching Google category, condition, availability, gender, age_group...
        $ret  = Db::getInstance()->executeS('
      SELECT k.*, g.*, gl.*
            FROM ' . _DB_PREFIX_ . 'category k
            LEFT JOIN ' . _DB_PREFIX_ . 'gshoppingflux g ON (g.id_gcategory=k.id_category AND g.id_shop=' . $id_shop . ')
            LEFT JOIN ' . _DB_PREFIX_ . 'gshoppingflux_lang gl ON (gl.id_gcategory=k.id_category AND gl.id_lang = ' . (int) $id_lang . ' AND gl.id_shop=' . (int) $id_shop . ')
            WHERE g.id_shop = ' . (int) $id_shop);
        $shop = new Shop($id_shop);
        $root = Category::getRootCategory($id_lang, $shop);
        
        foreach ($ret as $cat) {
            $parent_id    = $cat['id_category'];
            $gcategory    = $cat['gcategory'];
            $condition    = $cat['condition'];
            $availability = $cat['availability'];
            $gender       = $cat['gender'];
            $age_group    = $cat['age_group'];
            $color        = $cat['color'];
            $material     = $cat['material'];
            $pattern      = $cat['pattern'];
            $size         = $cat['size'];
            
            while ((empty($gcategory) || empty($condition) || empty($availability) || empty($gender) || empty($age_group) || empty($color) || empty($material) || empty($pattern) || empty($size)) && $parent_id >= $root->id_category) {
                $parentsql = $sql . ' AND k.id_category = ' . $parent_id . ';';
                $parentret = Db::getInstance()->executeS($parentsql);
                
                if (!count($parentret))
                    break;
                
                foreach ($parentret as $parentcat) {
                    $parent_id = $parentcat['id_parent'];
                    if (empty($gcategory))
                        $gcategory = $parentcat['gcategory'];
                    if (empty($condition))
                        $condition = $parentcat['condition'];
                    if (empty($availability))
                        $availability = $parentcat['availability'];
                    if (empty($gender))
                        $gender = $parentcat['gender'];
                    if (empty($age_group))
                        $age_group = $parentcat['age_group'];
                    if (empty($color))
                        $color = $parentcat['color'];
                    if (empty($material))
                        $material = $parentcat['material'];
                    if (empty($pattern))
                        $pattern = $parentcat['pattern'];
                    if (empty($size))
                        $size = $parentcat['size'];
                }
            }
            
            if (!$color && !empty($this->module_conf['color']))
                $color = $this->module_conf['color'];
            if (!$material && !$this->module_conf['material'])
                $material = $this->module_conf['material'];
            if (!$pattern && !empty($this->module_conf['pattern']))
                $pattern = $this->module_conf['pattern'];
            if (!$size && !$this->module_conf['size'])
                $size = $this->module_conf['size'];
            
            $this->categories_values[$cat['id_category']]['gcategory']       = html_entity_decode($gcategory);
            $this->categories_values[$cat['id_category']]['gcat_condition']  = $condition;
            $this->categories_values[$cat['id_category']]['gcat_avail']      = $availability;
            $this->categories_values[$cat['id_category']]['gcat_gender']     = $gender;
            $this->categories_values[$cat['id_category']]['gcat_age_group']  = $age_group;
            $this->categories_values[$cat['id_category']]['gcat_color[]']    = explode(";", $color);
            $this->categories_values[$cat['id_category']]['gcat_material[]'] = explode(";", $material);
            $this->categories_values[$cat['id_category']]['gcat_pattern[]']  = explode(";", $pattern);
            $this->categories_values[$cat['id_category']]['gcat_size[]']     = explode(";", $size);
        }
    }
    
    private function rip_tags($string)
    {
        // ----- remove HTML TAGs -----
        $string = preg_replace('/<[^>]*>/', ' ', $string);
        
        // ----- remove control characters -----
        $string = str_replace("\r", '', $string); // --- replace with empty space
        $string = str_replace("\n", ' ', $string); // --- replace with space
        $string = str_replace("\t", ' ', $string); // --- replace with space
        
        // ----- remove multiple spaces -----
        $string = trim(preg_replace('/ {2,}/', ' ', $string));
        return $string;
    }
    
    private function _getOutputFileName($lang, $shop)
    {
        return 'googleshopping-s' . $shop . '-' . $lang . '.xml';
    }
    
    public function getPriceDisplayMethod($id_shop)
    {
        $ret = Db::getInstance()->executeS('
      SELECT g.price_display_method
            FROM ' . _DB_PREFIX_ . 'group g
            LEFT JOIN ' . _DB_PREFIX_ . 'group_shop gs ON (g.id_group = gs.id_group)
            WHERE g.id_group=1 AND gs.id_shop = ' . (int) $id_shop);
        return ($ret[0]['price_display_method']);
    }
    
    public function getShopDescription($id_lang, $id_shop)
    {
        $ret = Db::getInstance()->executeS('
      SELECT ml.description
            FROM ' . _DB_PREFIX_ . 'meta_lang ml
            LEFT JOIN ' . _DB_PREFIX_ . 'meta m ON (m.id_meta = ml.id_meta)
            WHERE m.page="index"
        AND ml.id_shop = ' . (int) $id_shop . '
        AND ml.id_lang = ' . (int) $id_lang);
        return ($ret[0]['description']);
    }
    
    public function generateAllShopsFileList()
    {
        // Get all shops
        $shops = Shop::getShops(true, null, true);
        foreach ($shops as $i => $shop)
            $ret[$i] = $this->generateShopFileList($shop);
        
        return $ret;
    }
    
    public function generateShopFileList($shop)
    {
        // Get all shop languages
        $languages = Language::getLanguages();
        foreach ($languages as $i => $lang)
            $ret[$i] = $this->generateFile($lang, $shop);
        
        return $ret;
    }
    
    private function generateFile($lang, $id_shop)
    {
        $id_lang       = (int) $lang['id_lang'];
        $this->shop    = new Shop($id_shop);
        $root          = Category::getRootCategory($id_lang, $this->shop);
        $this->id_root = $root->id_category;
        
        // Get module configuration for this shop
        $this->module_conf = $this->getConfigFieldsValues($id_shop);
        
        // Init categories special attributes :
        // Google's matching category, gender, age_group, color_group, material, pattern, size...
        $this->getGCategValues($id_lang, $id_shop);
        
        // Init file_path value
        if ($this->module_conf['gen_file_in_root'])
            $generate_file_path = dirname(__FILE__) . '/../../' . $this->_getOutputFileName($lang['iso_code'], $id_shop);
        else
            $generate_file_path = dirname(__FILE__) . '/file_exports/' . $this->_getOutputFileName($lang['iso_code'], $id_shop);
        
        $meta = Meta::getMetaByPage('index', $id_lang);
        
        // Google Shopping XML
        $xml = '<?xml version="1.0" encoding="' . self::CHARSET . '" ?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n\n";
        $xml .= '<channel>' . "\n";
        // Shop name
        $xml .= '<title><![CDATA[' . $this->shop->name . ']]></title>' . "\n";
        // Shop description
        $xml .= '<description><![CDATA[' . $this->getShopDescription($id_lang, $id_shop) . ']]></description>' . "\n";
        $xml .= '<link href="' . htmlspecialchars($this->uri, self::REPLACE_FLAGS, self::CHARSET, false) . '" rel="alternate" type="text/html"/>' . "\n";
        $xml .= '<image>' . "\n";
        $xml .= '<title><![CDATA[' . Configuration::get('PS_SHOP_NAME') . ']]></title>' . "\n";
        $xml .= '<url>' . htmlspecialchars($this->context->link->getMediaLink(_PS_IMG_ . Configuration::get('PS_LOGO')), self::REPLACE_FLAGS, self::CHARSET, false) . '</url>' . "\n";
        $xml .= '<link>' . htmlspecialchars($this->uri, self::REPLACE_FLAGS, self::CHARSET, false) . '</link>' . "\n";
        $xml .= '</image>' . "\n";
        $xml .= '<modified>' . date('Y-m-d') . 'T01:01:01Z</modified>' . "\n";
        $xml .= '<author>' . "\n" . '<name>' . htmlspecialchars(Configuration::get('PS_SHOP_NAME'), self::REPLACE_FLAGS, self::CHARSET, false) . '</name>' . "\n" . '</author>' . "\n\n";
        
        $googleshoppingfile = fopen($generate_file_path, 'w');
        
        // Add UTF-8 byte order mark
        fwrite($googleshoppingfile, pack("CCC", 0xef, 0xbb, 0xbf));
        
        // File header
        fwrite($googleshoppingfile, $xml);
        
        $sql = 'SELECT p.*, pl.*, ps.id_category_default as category_default, gc.*, gl.* ' //fl.*,
            . 'FROM ' . _DB_PREFIX_ . 'product p ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'category c ON c.id_category = ps.id_category_default ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'gshoppingflux gc ON gc.id_gcategory = c.id_category ' . 'LEFT JOIN ' . _DB_PREFIX_ . 'gshoppingflux_lang gl ON gl.id_gcategory = gc.id_gcategory ' . 'WHERE p.active = 1 AND c.active = 1 AND gc.export = 1 ' . 'AND pl.id_lang=' . $id_lang . ' AND gl.id_lang=' . $id_lang;
        
        // Multishops filter
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(true, null, true)) > 1)
            $sql .= ' AND gc.id_shop = ' . $id_shop . ' AND pl.id_shop = ' . $id_shop . ' AND ps.id_shop = ' . $id_shop . ' AND gl.id_shop = ' . $id_shop;
        
        // Check EAN13
        if ($this->module_conf['no_gtin'] != 1)
            $sql .= ' AND p.ean13 != "" AND p.ean13 != 0';
        
        // Check BRAND
        if ($this->module_conf['no_brand'] != 1)
            $sql .= ' AND p.id_manufacturer != "" AND p.id_manufacturer != 0';
        
        $products                       = Db::getInstance()->ExecuteS($sql);
        $this->nb_total_products        = 0;
        $this->nb_not_exported_products = 0;
        $this->nb_combinations          = 0;
        $this->nb_prd_w_attr            = array();
        $context                        = Context::getContext();
        $context->language->id          = $id_lang;
        $context->shop->id              = $id_shop;
        
        foreach ($products as $product) {
            $p                     = new Product($product['id_product'], true, $id_lang, $id_shop, $context);
            $attributeCombinations = $p->getAttributeCombinations($id_lang);
            
            if ($this->module_conf['mpn_type'] == 'reference' && !empty($product['reference']))
                $product['pid'] = $product['reference'];
            else if ($this->module_conf['mpn_type'] == 'supplier_reference' && !empty($product['supplier_reference']))
                $product['pid'] = $product['supplier_reference'];
            else
                $product['pid'] = $product['id_product'];
            $product['gid'] = $product['pid'];
            
            $xml_googleshopping = $this->getItemXML($product, $lang, $id_shop);
            fwrite($googleshoppingfile, $xml_googleshopping);
            
            if (count($attributeCombinations) > 0 && $this->module_conf['export_attributes'] == 1) {
                $attr = array();
                foreach ($attributeCombinations as $a => $attribute) {
                    $attr[$attribute['id_product_attribute']][$attribute['id_attribute_group']] = $attribute;
                }
                
                foreach ($attr as $id_attr => $v) {
                    foreach ($v as $k => $a) {
                        foreach ($this->categories_values[$product['id_gcategory']]['gcat_color[]'] as $c) {
                            if ($k == $c) {
                                $product['color'] = $a['attribute_name'];
                            }
                        }
                        foreach ($this->categories_values[$product['id_gcategory']]['gcat_material[]'] as $c) {
                            if ($k == $c) {
                                $product['material'] = $a['attribute_name'];
                            }
                        }
                        foreach ($this->categories_values[$product['id_gcategory']]['gcat_pattern[]'] as $c) {
                            if ($k == $c) {
                                $product['pattern'] = $a['attribute_name'];
                            }
                        }
                        foreach ($this->categories_values[$product['id_gcategory']]['gcat_size[]'] as $c) {
                            if ($k == $c) {
                                $product['size'] = $a['attribute_name'];
                            }
                        }
                        $product['quantity'] = $a['quantity'];
                        $product['weight']   = $a['weight'];
                    }
                    
                    if (empty($product['color']) && empty($product['material']) && empty($product['pattern']) && empty($product['size']))
                        continue 2;
                    
                    $product['item_group_id'] = $product['pid'];
                    $product['gid']           = $product['pid'] . '-' . $id_attr;
                    $xml_googleshopping       = $this->getItemXML($product, $lang, $id_shop, $id_attr);
                    fwrite($googleshoppingfile, $xml_googleshopping);
                    $product['color']    = '';
                    $product['material'] = '';
                    $product['pattern']  = '';
                    $product['size']     = '';
                }
            }
        }
        
        $xml = '</channel>' . "\n" . '</rss>';
        fwrite($googleshoppingfile, $xml);
        fclose($googleshoppingfile);
        
        @chmod($generate_file_path, 0777);
        return array(
            'nb_products' => $this->nb_total_products,
            'nb_combinations' => $this->nb_combinations,
            'nb_prod_w_attr' => count($this->nb_prd_w_attr),
            'non_exported_products' => $this->nb_not_exported_products
        );
    }
    
    private function getItemXML($product, $lang, $id_shop, $combination = false)
    {
        $xml_googleshopping    = '';
        $id_lang               = (int) $lang['id_lang'];
        $title_limit           = 70;
        $description_limit     = 10000;
        $languages             = Language::getLanguages();
        $tailleTabLang         = sizeof($languages);
        $context               = Context::getContext();
        $context->language->id = $id_lang;
        $context->shop->id     = $id_shop;
        $p                     = new Product($product['id_product'], true, $id_lang, $id_shop, $context);
        
        // Get module configuration for this shop
        //$this->module_conf = $this->getConfigFieldsValues($id_shop);
        if (!$combination)
            $product['quantity'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], 0, $id_shop);
        
        // Exclude non-available products
        if ($this->module_conf['export_nap'] === 0 && $product['quantity'] < 1) {
            $this->nb_not_exported_products++;
            return;
        }
        
        // Check minimum product price
        $price = Product::getPriceStatic((int) $product['id_product'], true);
        if ((float) $this->module_conf['export_min_price'] > 0 && (float) $this->module_conf['export_min_price'] > (float) $price)
            return;
        
        $cat_link_rew = Category::getLinkRewrite($product['id_gcategory'], intval($lang));
        $product_link = $this->context->link->getProductLink((int) ($product['id_product']), $product['link_rewrite'], $cat_link_rew, $product['ean13'], (int) ($product['id_lang']), $id_shop, $combination, true);
        
        // Product name
        $title_crop = ucfirst(mb_strtolower($product['name'], self::CHARSET));
        
        //  Product color attribute, if any
        if (!empty($product['color']))
            $title_crop .= ' ' . $product['color'];
        if (!empty($product['material']))
            $title_crop .= ' ' . $product['material'];
        if (!empty($product['pattern']))
            $title_crop .= ' ' . $product['pattern'];
        if (!empty($product['size']))
            $title_crop .= ' ' . $product['size'];
        
        if (strlen($product['name']) > $title_limit) {
            $title_crop = substr($title_crop, 0, ($title_limit - 1));
            $title_crop = substr($title_crop, 0, strrpos($title_crop, " "));
        }
        
        // Description type
        if ($this->module_conf['description'] == 'long')
            $description_crop = $product['description'];
        
        else if ($this->module_conf['description'] == 'short')
            $description_crop = $product['description_short'];
        
        else if ($this->module_conf['description'] == 'meta')
            $description_crop = $product['meta_description'];
        
        $description_crop = $this->rip_tags($description_crop);
        
        if (strlen($description_crop) > $description_limit) {
            $description_crop = substr($description_crop, 0, ($description_limit - 1));
            $description_crop = substr($description_crop, 0, strrpos($description_crop, " ")) . ' ...';
        }
        
        $xml_googleshopping .= '<item>' . "\n";
        $xml_googleshopping .= '<g:id>' . $product['gid'] . '</g:id>' . "\n";
        $xml_googleshopping .= '<title><![CDATA[' . $title_crop . ']]></title>' . "\n";
        $xml_googleshopping .= '<description><![CDATA[' . $description_crop . ']]></description>' . "\n";
        $xml_googleshopping .= '<link><![CDATA[' . htmlspecialchars($product_link, self::REPLACE_FLAGS, self::CHARSET, false) . ']]></link>' . "\n";
        
        // Image links
        $images       = Image::getImages($lang['id_lang'], $product['id_product'], $combination);
        $indexTabLang = 0;
        if ($tailleTabLang > 1) {
            while (sizeof($images) < 1 && $indexTabLang < $tailleTabLang) {
                if ($languages[$indexTabLang]['id_lang'] != $lang['id_lang'])
                    $images = Image::getImages($languages[$indexTabLang]['id_lang'], $product['id_product']);
                
                $indexTabLang++;
            }
        }
        $nbimages   = 0;
        $image_type = $this->module_conf['img_type'];
        
        if ($image_type == '')
            $image_type = 'large_default';
        
        foreach ($images as $im) {
            $image = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'] . '-' . $im['id_image'], $image_type);
            $image = preg_replace('*http://' . Tools::getHttpHost() . '/*', $this->uri, $image);
            $xml_googleshopping .= '<g:image_link><![CDATA[' . $image . ']]></g:image_link>' . "\n";
            // max images by product
            if (++$nbimages == 10)
                break;
        }
        
        // Product condition, or category's condition attribute, or its parent one...
        // Product condition = new, used, refurbished
        if (empty($product['condition']))
            $product['condition'] = $this->categories_values[$product['id_gcategory']]['gcat_condition'];
        
        if (!empty($product['condition']))
            $xml_googleshopping .= '<g:condition><![CDATA[' . $product['condition'] . ']]></g:condition>' . "\n";
        
        // Shop category
        $breadcrumb   = GCategories::getPath($product['id_gcategory'], '', $id_lang, $id_shop, $this->id_root);
        $product_type = '';
        
        if (!empty($this->module_conf['product_type[]'][$id_lang])) {
            $product_type = $this->module_conf['product_type[]'][$id_lang];
            
            if (!empty($breadcrumb))
                $product_type .= " > ";
        }
        
        $product_type .= $breadcrumb;
        $xml_googleshopping .= '<g:product_type><![CDATA[' . $product_type . ']]></g:product_type>' . "\n";
        
        // Matching Google category, or parent categories' one
        $product['gcategory'] = $this->categories_values[$product['category_default']]['gcategory'];
        $xml_googleshopping .= '<g:google_product_category><![CDATA[' . $product['gcategory'] . ']]></g:google_product_category>' . "\n";
        
        // Product quantity & availability
        if (empty($this->categories_values[$product['category_default']]['gcat_avail'])) {
            if ($this->module_conf['quantity'] == 1)
                $xml_googleshopping .= '<g:quantity>' . $product['quantity'] . '</g:quantity>' . "\n";
            
            if ($product['quantity'] > 0 && $product['available_for_order'])
                $xml_googleshopping .= '<g:availability>in stock</g:availability>' . "\n";
            
            else
                $xml_googleshopping .= '<g:availability>out of stock</g:availability>' . "\n";
        }
        
        else {
            if ($this->module_conf['quantity'] == 1 && $product['quantity'] > 0)
                $xml_googleshopping .= '<g:quantity>' . $product['quantity'] . '</g:quantity>' . "\n";
            $xml_googleshopping .= '<g:availability>' . $this->categories_values[$product['category_default']]['gcat_avail'] . '</g:availability>' . "\n";
        }
        
        // Price(s)
        $currency             = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $use_tax              = ($this->getPriceDisplayMethod($id_shop) ? false : true);
        $no_tax               = (!$use_tax ? true : false);
        $price                = number_format(round($p->getPriceStatic($product['id_product'], $use_tax, $combination, 2), 2), 2, '.', ' ');
        $price_without_reduct = number_format(round($p->getPriceWithoutReduct($no_tax, $combination), 2), 2, '.', ' ');
        if ((float) ($price) < (float) ($price_without_reduct)) {
            $xml_googleshopping .= '<g:price>' . $price_without_reduct . ' ' . $currency->iso_code . '</g:price>' . "\n";
            $xml_googleshopping .= '<g:sale_price>' . $price . ' ' . $currency->iso_code . '</g:sale_price>' . "\n";
        } else {
            $xml_googleshopping .= '<g:price>' . $price . ' ' . $currency->iso_code . '</g:price>' . "\n";
        }
        
        $identifier_exists = 0;
        // GTIN (EAN, UPC, JAN, ISBN)
        if ($this->module_conf['no_gtin'] != 1 && $product['ean13'] != '' && $product['ean13'] != '0') {
            $xml_googleshopping .= '<g:gtin>' . $product['ean13'] . '</g:gtin>' . "\n";
            $identifier_exists++;
        }
        
        // Brand
        if ($this->module_conf['no_brand'] != 0 && !empty($product['id_manufacturer'])) {
            $xml_googleshopping .= '<g:brand><![CDATA[' . htmlspecialchars(Manufacturer::getNameById(intval($product['id_manufacturer'])), self::REPLACE_FLAGS, self::CHARSET, false) . ']]></g:brand>' . "\n";
            $identifier_exists++;
        }
        
        // MPN
        if (empty($product['supplier_reference']))
            $product['supplier_reference'] = ProductSupplier::getProductSupplierReference($product['id_product'], 0, $product['id_supplier']);
        
        if ($this->module_conf['mpn_type'] == 'reference' && !empty($product['reference'])) {
            $xml_googleshopping .= '<g:mpn><![CDATA[' . $product['reference'] . ']]></g:mpn>' . "\n";
            $identifier_exists++;
        }
        
        else if ($this->module_conf['mpn_type'] == 'supplier_reference' && !empty($product['supplier_reference'])) {
            $xml_googleshopping .= '<g:mpn><![CDATA[' . $product['supplier_reference'] . ']]></g:mpn>' . "\n";
            $identifier_exists++;
        }
        
        // Tag "identifier_exists"
        if ($this->module_conf['id_exists_tag'] && $identifier_exists < 2) {
            $xml_googleshopping .= '<g:identifier_exists>FALSE</g:identifier_exists>' . "\n";
        }
        
        // Product gender and age_group attributes association
        $product_features     = $this->getProductFeatures($product['id_product'], $id_lang, $id_shop);
        $product['gender']    = $this->categories_values[$product['category_default']]['gcat_gender'];
        $product['age_group'] = $this->categories_values[$product['category_default']]['gcat_age_group'];
        foreach ($product_features as $f => $feature) {
            switch ($feature['id_feature']) {
                case $this->module_conf['gender']:
                    $product['gender'] = $feature['value'];
                    continue 2;
                
                case $this->module_conf['age_group']:
                    $product['age_group'] = $feature['value'];
                    continue 2;
            }
            
            if (!$product['color']) {
                foreach ($this->module_conf['color[]'] as $id => $v)
                    if ($v == $feature['id_feature'])
                        $product['color'] = $feature['value'];
            }
            if (!$product['material']) {
                foreach ($this->module_conf['material[]'] as $id => $v)
                    if ($v == $feature['id_feature'])
                        $product['material'] = $feature['value'];
            }
            if (!$product['pattern']) {
                foreach ($this->module_conf['pattern[]'] as $id => $v)
                    if ($v == $feature['id_feature'])
                        $product['pattern'] = $feature['value'];
            }
            if (!$product['size']) {
                foreach ($this->module_conf['size[]'] as $id => $v)
                    if ($v == $feature['id_feature'])
                        $product['size'] = $feature['value'];
            }
        }
        
        //  Product gender attribute, or category gender attribute, or parent's one
        if (!empty($product['gender']))
            $xml_googleshopping .= '<g:gender><![CDATA[' . $product['gender'] . ']]></g:gender>' . "\n";
        
        // Product age_group attribute, or category age_group attribute, or parent's one
        if (!empty($product['age_group']))
            $xml_googleshopping .= '<g:age_group><![CDATA[' . $product['age_group'] . ']]></g:age_group>' . "\n";
        
        // Product attributes combination groups
        if ($combination && !empty($product['item_group_id']))
            $xml_googleshopping .= '<g:item_group_id><![CDATA[' . $product['item_group_id'] . ']]></g:item_group_id>' . "\n";
        
        // Product color attribute, or category color attribute, or parent's one
        if (!empty($product['color']))
            $xml_googleshopping .= '<g:color><![CDATA[' . $product['color'] . ']]></g:color>' . "\n";
        
        // Product material attribute, or category material attribute, or parent's one
        if (!empty($product['material']))
            $xml_googleshopping .= '<g:material><![CDATA[' . $product['material'] . ']]></g:material>' . "\n";
        
        // Product color attribute, or category color attribute, or parent's one
        if (!empty($product['pattern']))
            $xml_googleshopping .= '<g:pattern><![CDATA[' . $product['pattern'] . ']]></g:pattern>' . "\n";
        
        // Product material attribute, or category material attribute, or parent's one
        if (!empty($product['size']))
            $xml_googleshopping .= '<g:size><![CDATA[' . $product['size'] . ']]></g:size>' . "\n";
        
        // Featured products
        if ($this->module_conf['featured_products'] == 1 && $product['on_sale'] != '0') {
            $xml_googleshopping .= '<g:featured_product>true</g:featured_product>' . "\n";
        }
        
        // Shipping
        $xml_googleshopping .= '<g:shipping>' . "\n";
        $xml_googleshopping .= "\t" . '<g:country>' . $this->module_conf['shipping_country'] . '</g:country>' . "\n";
        $xml_googleshopping .= "\t" . '<g:service>Standard</g:service>' . "\n";
        $xml_googleshopping .= "\t" . '<g:price>' . number_format($this->module_conf['shipping_price'], 2, '.', ' ') . ' ' . $currency->iso_code . '</g:price>' . "\n";
        $xml_googleshopping .= '</g:shipping>' . "\n";
        
        // Shipping weight
        if ($product['weight'] != '0') {
            $xml_googleshopping .= '<g:shipping_weight>' . number_format($product['weight'], 2, '.', '') . ' ' . Configuration::get('PS_WEIGHT_UNIT') . '</g:shipping_weight>' . "\n";
        }
        
        $xml_googleshopping .= '</item>' . "\n\n";
        
        if ($combination) {
            $this->nb_combinations++;
            $this->nb_prd_w_attr[$product['id_product']] = 1;
        }
        $this->nb_total_products++;
        
        return $xml_googleshopping;
    }
}
Download Formatting took: 797 ms
PHP Formatter made by Spark Labs  
Copyright Gerben van Veenendaal  
