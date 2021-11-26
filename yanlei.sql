/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 80012
 Source Host           : localhost:3306
 Source Schema         : yanlei

 Target Server Type    : MySQL
 Target Server Version : 80012
 File Encoding         : 65001

 Date: 13/10/2021 08:27:20
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tp_access
-- ----------------------------
DROP TABLE IF EXISTS `tp_access`;
CREATE TABLE `tp_access`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员表id',
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色表id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_access
-- ----------------------------
INSERT INTO `tp_access` VALUES (1, 1, 1, 0, 0);
INSERT INTO `tp_access` VALUES (2, 2, 1, 0, 0);

-- ----------------------------
-- Table structure for tp_admin
-- ----------------------------
DROP TABLE IF EXISTS `tp_admin`;
CREATE TABLE `tp_admin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `nickname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱账号',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '账号密码',
  `mobile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `is_use` tinyint(255) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用：0否，1是',
  `last_login_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `lang` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '语言类型',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_admin
-- ----------------------------
INSERT INTO `tp_admin` VALUES (1, 'admin', '/upload/2021-03-02/603ddb10c5579.jpg', 'admin@qq.com', '$2y$10$5fSzcLlFkK4hq6oTQvYEcudbDcrEJYBFlPDkH20x4jhPju02wruEW', '18396837961', '管理员', 1, '112.231.121.39', 0, 'en-us', 1608013736, 1616397022);
INSERT INTO `tp_admin` VALUES (2, 'vWI7E22Y', '/static/img/default_avatar.png', 'tec53@goldencell.biz', '$2y$10$jIi02.NfGAQr/0wjy7uOpuGSHZvY3VEi7XcP/wQZxct9eDxKuFcXq', '', '', 1, '223.104.191.175', 0, 'en-us', 1608712304, 1614929409);

-- ----------------------------
-- Table structure for tp_agent
-- ----------------------------
DROP TABLE IF EXISTS `tp_agent`;
CREATE TABLE `tp_agent`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员表id',
  `nickname` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '代理商昵称',
  `avatar` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '代理商头像',
  `email` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `password` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `mobile` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '代理商真实姓名',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '地址',
  `linkman` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系人',
  `is_use` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用：0否，1是',
  `last_login_ip` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '最后登录时间',
  `lang` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '语言类型',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '代理商表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_agent
-- ----------------------------
INSERT INTO `tp_agent` VALUES (4, 2, '精工研发', '/upload/2021-01-05/5ff414b6a7376.png', 'tec53@goldencell.biz', '11ce2b1bed11f9e64aa86f728f2824f9', '13562462509', '精工研发', '山东省枣庄市高新区', '陈兵', 1, '223.104.191.175', '1614933891', 'en-us', 1609831608, 1614933891);
INSERT INTO `tp_agent` VALUES (5, 1, '代理商昵称', '/upload/2021-03-02/603ddd0eeb718.jpg', '15910109400@163.com', 'e10adc3949ba59abbe56e057f20f883e', '15910109400', '代理商测试', '山东省枣庄市高新区', '张夏夏', 1, '112.232.39.178', '1614668908', 'en-us', 1614667079, 1614668908);

-- ----------------------------
-- Table structure for tp_captcha
-- ----------------------------
DROP TABLE IF EXISTS `tp_captcha`;
CREATE TABLE `tp_captcha`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `mobile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '邮箱',
  `captcha` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '验证码',
  `expire_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '过期时间',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '验证码表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_captcha
-- ----------------------------
INSERT INTO `tp_captcha` VALUES (1, '', 'tec02@goldencell.biz', '4860', 1610162901, 1610162836, 1610162901);
INSERT INTO `tp_captcha` VALUES (2, '', '15910109322@163.com', '4501', 1613972970, 1613972936, 1613972970);

-- ----------------------------
-- Table structure for tp_dictionary
-- ----------------------------
DROP TABLE IF EXISTS `tp_dictionary`;
CREATE TABLE `tp_dictionary`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '字典表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_dictionary
-- ----------------------------

-- ----------------------------
-- Table structure for tp_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_log`;
CREATE TABLE `tp_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型：1管理员，2用户',
  `operator_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作员id',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '日志表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_log
-- ----------------------------
INSERT INTO `tp_log` VALUES (1, 3, 2, 'login', 1609913427, 1609913427);
INSERT INTO `tp_log` VALUES (2, 3, 2, 'login', 1609918539, 1609918539);
INSERT INTO `tp_log` VALUES (3, 1, 1, 'login', 1609921350, 1609921350);
INSERT INTO `tp_log` VALUES (4, 2, 4, 'login', 1609921509, 1609921509);
INSERT INTO `tp_log` VALUES (5, 1, 2, 'login', 1609921533, 1609921533);
INSERT INTO `tp_log` VALUES (6, 1, 1, 'login', 1609925222, 1609925222);
INSERT INTO `tp_log` VALUES (7, 1, 1, 'login', 1610004652, 1610004652);
INSERT INTO `tp_log` VALUES (8, 3, 2, 'login', 1610013373, 1610013373);
INSERT INTO `tp_log` VALUES (9, 3, 2, 'modify account', 1610013529, 1610013529);
INSERT INTO `tp_log` VALUES (10, 3, 2, 'modify account', 1610013747, 1610013747);
INSERT INTO `tp_log` VALUES (11, 3, 2, 'modify account', 1610013764, 1610013764);
INSERT INTO `tp_log` VALUES (12, 1, 2, 'login', 1610065320, 1610065320);
INSERT INTO `tp_log` VALUES (13, 2, 4, 'login', 1610066320, 1610066320);
INSERT INTO `tp_log` VALUES (14, 3, 6, 'login', 1610066340, 1610066340);
INSERT INTO `tp_log` VALUES (15, 1, 1, 'login', 1610066849, 1610066849);
INSERT INTO `tp_log` VALUES (16, 1, 1, 'login', 1610070696, 1610070696);
INSERT INTO `tp_log` VALUES (17, 3, 6, 'login', 1610073785, 1610073785);
INSERT INTO `tp_log` VALUES (18, 2, 4, 'login', 1610073834, 1610073834);
INSERT INTO `tp_log` VALUES (19, 1, 2, 'login', 1610073888, 1610073888);
INSERT INTO `tp_log` VALUES (20, 1, 1, 'login', 1610076494, 1610076494);
INSERT INTO `tp_log` VALUES (21, 1, 2, 'login', 1610086882, 1610086882);
INSERT INTO `tp_log` VALUES (22, 3, 6, 'login', 1610087317, 1610087317);
INSERT INTO `tp_log` VALUES (23, 1, 1, 'login', 1610088603, 1610088603);
INSERT INTO `tp_log` VALUES (24, 1, 1, 'login', 1610088631, 1610088631);
INSERT INTO `tp_log` VALUES (25, 1, 2, 'login', 1610106826, 1610106826);
INSERT INTO `tp_log` VALUES (26, 1, 2, 'login', 1610152244, 1610152244);
INSERT INTO `tp_log` VALUES (27, 2, 4, 'login', 1610152266, 1610152266);
INSERT INTO `tp_log` VALUES (28, 3, 6, 'login', 1610152281, 1610152281);
INSERT INTO `tp_log` VALUES (29, 1, 2, 'login', 1610154978, 1610154978);
INSERT INTO `tp_log` VALUES (30, 3, 6, 'login', 1610155413, 1610155413);
INSERT INTO `tp_log` VALUES (31, 1, 1, 'login', 1610159080, 1610159080);
INSERT INTO `tp_log` VALUES (32, 1, 2, 'login', 1610162147, 1610162147);
INSERT INTO `tp_log` VALUES (33, 1, 2, 'login', 1610163646, 1610163646);
INSERT INTO `tp_log` VALUES (34, 1, 1, 'login', 1610170672, 1610170672);
INSERT INTO `tp_log` VALUES (35, 1, 2, 'login', 1610349660, 1610349660);
INSERT INTO `tp_log` VALUES (36, 1, 2, 'login', 1610440462, 1610440462);
INSERT INTO `tp_log` VALUES (37, 3, 6, 'login', 1610441507, 1610441507);
INSERT INTO `tp_log` VALUES (38, 3, 6, 'login', 1610509679, 1610509679);
INSERT INTO `tp_log` VALUES (39, 3, 6, 'login', 1610588013, 1610588013);
INSERT INTO `tp_log` VALUES (40, 1, 1, 'login', 1610672057, 1610672057);
INSERT INTO `tp_log` VALUES (41, 1, 1, 'modify account', 1610672078, 1610672078);
INSERT INTO `tp_log` VALUES (42, 1, 1, 'login', 1610675905, 1610675905);
INSERT INTO `tp_log` VALUES (43, 1, 1, 'modify account', 1610675952, 1610675952);
INSERT INTO `tp_log` VALUES (44, 2, 3, 'login', 1610696177, 1610696177);
INSERT INTO `tp_log` VALUES (45, 3, 2, 'login', 1610696231, 1610696231);
INSERT INTO `tp_log` VALUES (46, 3, 2, 'login', 1610699584, 1610699584);
INSERT INTO `tp_log` VALUES (47, 3, 2, 'login', 1610784409, 1610784409);
INSERT INTO `tp_log` VALUES (48, 3, 2, 'modify account', 1610784541, 1610784541);
INSERT INTO `tp_log` VALUES (49, 3, 2, 'login', 1610932143, 1610932143);
INSERT INTO `tp_log` VALUES (50, 3, 2, 'login', 1610958027, 1610958027);
INSERT INTO `tp_log` VALUES (51, 1, 2, 'login', 1611126615, 1611126615);
INSERT INTO `tp_log` VALUES (52, 1, 1, 'login', 1611134871, 1611134871);
INSERT INTO `tp_log` VALUES (53, 1, 1, 'login', 1611135415, 1611135415);
INSERT INTO `tp_log` VALUES (54, 1, 1, 'login', 1611190267, 1611190267);
INSERT INTO `tp_log` VALUES (55, 1, 1, 'login', 1611196858, 1611196858);
INSERT INTO `tp_log` VALUES (56, 3, 3, 'login', 1611200738, 1611200738);
INSERT INTO `tp_log` VALUES (57, 1, 1, 'login', 1611372235, 1611372235);
INSERT INTO `tp_log` VALUES (58, 1, 1, 'login', 1611372895, 1611372895);
INSERT INTO `tp_log` VALUES (59, 1, 1, 'login', 1613963449, 1613963449);
INSERT INTO `tp_log` VALUES (60, 1, 2, 'login', 1614057517, 1614057517);
INSERT INTO `tp_log` VALUES (61, 3, 2, 'login', 1614666192, 1614666192);
INSERT INTO `tp_log` VALUES (62, 3, 2, 'login', 1614666281, 1614666281);
INSERT INTO `tp_log` VALUES (63, 3, 2, 'login', 1614666353, 1614666353);
INSERT INTO `tp_log` VALUES (64, 3, 2, 'modify account', 1614666398, 1614666398);
INSERT INTO `tp_log` VALUES (65, 1, 1, 'login', 1614666443, 1614666443);
INSERT INTO `tp_log` VALUES (66, 1, 1, 'modify account', 1614666514, 1614666514);
INSERT INTO `tp_log` VALUES (67, 2, 5, 'login', 1614667194, 1614667194);
INSERT INTO `tp_log` VALUES (68, 3, 10, 'login', 1614667422, 1614667422);
INSERT INTO `tp_log` VALUES (69, 1, 1, 'login', 1614668847, 1614668847);
INSERT INTO `tp_log` VALUES (70, 3, 10, 'login', 1614668881, 1614668881);
INSERT INTO `tp_log` VALUES (71, 2, 5, 'login', 1614668908, 1614668908);
INSERT INTO `tp_log` VALUES (72, 3, 3, 'login', 1614675161, 1614675161);
INSERT INTO `tp_log` VALUES (73, 1, 1, 'modify account', 1614733109, 1614733109);
INSERT INTO `tp_log` VALUES (74, 1, 1, 'login', 1614738716, 1614738716);
INSERT INTO `tp_log` VALUES (75, 1, 1, 'login', 1614741104, 1614741104);
INSERT INTO `tp_log` VALUES (76, 2, 4, 'login', 1614741473, 1614741473);
INSERT INTO `tp_log` VALUES (77, 3, 6, 'login', 1614741632, 1614741632);
INSERT INTO `tp_log` VALUES (78, 3, 2, 'login', 1614763230, 1614763230);
INSERT INTO `tp_log` VALUES (79, 3, 2, 'login', 1614763257, 1614763257);
INSERT INTO `tp_log` VALUES (80, 1, 2, 'login', 1614929409, 1614929409);
INSERT INTO `tp_log` VALUES (81, 1, 1, 'login', 1614929508, 1614929508);
INSERT INTO `tp_log` VALUES (82, 3, 6, 'login', 1614929580, 1614929580);
INSERT INTO `tp_log` VALUES (83, 2, 4, 'login', 1614929602, 1614929602);
INSERT INTO `tp_log` VALUES (84, 2, 4, 'login', 1614929840, 1614929840);
INSERT INTO `tp_log` VALUES (85, 1, 1, 'login', 1614931781, 1614931781);
INSERT INTO `tp_log` VALUES (86, 1, 1, 'login', 1614932868, 1614932868);
INSERT INTO `tp_log` VALUES (87, 1, 1, 'login', 1614932890, 1614932890);
INSERT INTO `tp_log` VALUES (88, 2, 4, 'login', 1614933891, 1614933891);
INSERT INTO `tp_log` VALUES (89, 2, 6, 'login', 1614934272, 1614934272);
INSERT INTO `tp_log` VALUES (90, 1, 1, 'login', 1614934443, 1614934443);
INSERT INTO `tp_log` VALUES (91, 1, 1, 'login', 1614935647, 1614935647);
INSERT INTO `tp_log` VALUES (92, 1, 1, 'login', 1615188097, 1615188097);
INSERT INTO `tp_log` VALUES (93, 1, 1, 'login', 1615276120, 1615276120);
INSERT INTO `tp_log` VALUES (94, 1, 1, 'login', 1615282154, 1615282154);
INSERT INTO `tp_log` VALUES (95, 3, 12, 'login', 1615782194, 1615782194);
INSERT INTO `tp_log` VALUES (96, 1, 1, 'login', 1615784132, 1615784132);
INSERT INTO `tp_log` VALUES (97, 3, 12, 'login', 1615785073, 1615785073);
INSERT INTO `tp_log` VALUES (98, 3, 12, 'login', 1616135846, 1616135846);
INSERT INTO `tp_log` VALUES (99, 3, 12, 'login', 1616136366, 1616136366);
INSERT INTO `tp_log` VALUES (100, 3, 12, 'login', 1616144235, 1616144235);
INSERT INTO `tp_log` VALUES (101, 1, 1, 'login', 1616397022, 1616397022);

-- ----------------------------
-- Table structure for tp_message
-- ----------------------------
DROP TABLE IF EXISTS `tp_message`;
CREATE TABLE `tp_message`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `send_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送者类型：1管理员，2用户',
  `send_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送者id',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `receive_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收者类型：1管理员，2用户',
  `receive_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收者id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '消息表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_message
-- ----------------------------
INSERT INTO `tp_message` VALUES (1, 1, 1, '消息发布', '消息发布测试', 2, 1, 1609319863, 1609319863);
INSERT INTO `tp_message` VALUES (2, 1, 2, 'efdf', 'dffdfd', 2, 4, 1610155324, 1610155324);

-- ----------------------------
-- Table structure for tp_notice
-- ----------------------------
DROP TABLE IF EXISTS `tp_notice`;
CREATE TABLE `tp_notice`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '公告表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_notice
-- ----------------------------
INSERT INTO `tp_notice` VALUES (1, '公告发布', '<p>方式方法付付付付付付付付付付付</p><p><img src=\"/upload/2020-12-30/ueditor1609319904170258.jpg\" title=\"ueditor1609319904170258.jpg\"/></p><p><img src=\"/upload/2020-12-30/ueditor1609319905159078.jpg\" title=\"ueditor1609319905159078.jpg\"/></p><p><br/></p>', 1609319908, 1609319908);
INSERT INTO `tp_notice` VALUES (2, '公告', '<p>啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊&nbsp; 见风使舵来回顾枫林华府卡拉女考虑换个GFH啊金凤凰LF很费劲老客户F混分巨兽FS和开发<br/></p><p>HSJFHSAGFLA GFA G还是干花快结束了开的还是的发送到开发还得分拉横幅发神经开发费发件安徽发大水fsa</p><p>付货款就沙发厉害了快交付</p><p>&nbsp; &nbsp;很干净的后宫佳丽看电话挂了都是来块刚好够抠脚大汉过多时刚变得更好路口就是公司的话公交卡读后感十多个肯定是g</p><p><img src=\"/upload/2020-12-30/ueditor1609319983390409.jpg\" title=\"ueditor1609319983390409.jpg\" alt=\"电脑壁纸.jpg\"/></p>', 1609319986, 1609319986);
INSERT INTO `tp_notice` VALUES (3, '666', '<p>开机后的开关抠脚大汉干的噶多看几个华来看个爱过哈哈刚来肯定个矮冬瓜快到家了肯德基放垃圾费拉设计费雷克萨荆防颗粒世纪东方开始减肥撒酒疯副科级说服力哈是开发阿发件方律师费</p><p>倍科就付了三件赶快来卡机管理卡家里看到集卡离得进了卡记录卡换个发了个记录卡改好了卡就管理会计阿斯古丽看见了开关机埃里克独孤伽罗卡就管理卡就光拉管饭了卡解放啦</p>', 1609320361, 1609320361);
INSERT INTO `tp_notice` VALUES (4, '和感哈哈哈', '<p style=\"text-align: center;\">华工科技很快就很快就；；看了h改的嘎嘎三个萨嘎说的啊发所多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多多<br/></p>', 1609320410, 1609320410);

-- ----------------------------
-- Table structure for tp_region
-- ----------------------------
DROP TABLE IF EXISTS `tp_region`;
CREATE TABLE `tp_region`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `level` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '层级',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '地区表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_region
-- ----------------------------
INSERT INTO `tp_region` VALUES (1, 0, '济南', 0, 1608080964, 1608080964);
INSERT INTO `tp_region` VALUES (2, 0, '枣庄', 0, 1608712789, 1608712789);
INSERT INTO `tp_region` VALUES (3, 1, '长清', 1, 1608712810, 1608712810);
INSERT INTO `tp_region` VALUES (5, 2, '枣庄市高新区', 1, 1608712902, 1608713114);
INSERT INTO `tp_region` VALUES (6, 2, '薛城区', 1, 1608712921, 1608712921);
INSERT INTO `tp_region` VALUES (7, 0, 'zzz', 0, 1610155289, 1610155289);

-- ----------------------------
-- Table structure for tp_role
-- ----------------------------
DROP TABLE IF EXISTS `tp_role`;
CREATE TABLE `tp_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色拥有的规则id， 多个规则\",\"隔开',
  `is_use` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用：0否，1是',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '角色表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_role
-- ----------------------------
INSERT INTO `tp_role` VALUES (1, '管理员', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90', 1, 1608013736, 1608013736);

-- ----------------------------
-- Table structure for tp_rule
-- ----------------------------
DROP TABLE IF EXISTS `tp_rule`;
CREATE TABLE `tp_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则标识',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示：0否， 1是',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级id',
  `is_menu` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为菜单：0否，1是',
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '路径，逗号分隔',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 87 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '规则表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_rule
-- ----------------------------
INSERT INTO `tp_rule` VALUES (1, 'fa-home', 'index/index', '首页', 1, 0, 0, '0,', 0, 1594290113);
INSERT INTO `tp_rule` VALUES (2, 'fa-crosshairs', 'index/search', '首页搜索', 1, 0, 0, '', 0, 1597736365);
INSERT INTO `tp_rule` VALUES (3, '', 'index/map', '首页地图', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (4, '', 'index/lists', '首页列表', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (5, '', 'index/change_status', '改变状态', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (6, '', 'index/online_upgrade', '在线升级', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (7, '', 'equipment/index', '设备管理首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (8, '', 'equipment/search', '设备管理搜索', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (9, '', 'equipment/lists', '设备管理列表', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (10, '', 'equipment/change_status', '改变状态', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (11, '', 'equipment/online_upgrade', '在线升级', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (12, '', 'agent/index', '代理商管理首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (13, '', 'agent/addEdit', '添加/修改代理商', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (14, '', 'agent/isUse', '禁用/解禁代理商', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (15, '', 'agent/delete', '删除代理商', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (16, '', 'agent/import', '批量导入代理商设备', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (17, '', 'user/index', '用户管理首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (18, '', 'user/isUse', '禁用/解禁用户', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (19, '', 'user/delete', '删除用户', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (20, '', 'user/changePassword', '修改用户密码', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (21, '', 'EquipmentRepair/index', '报修管理首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (22, '', 'Agent/upload', '上传文件', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (23, '', 'EquipmentRepair/getFieldValue', '获取设备报修记录的字段值', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (24, '', 'EquipmentRepair/delete', '删除设备报修记录', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (25, '', 'SystemSetup/index', '系统设置', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (26, '', 'ParamSetup/addEdit', '参数设置', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (27, '', 'Admin/index', '管理员列表', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (28, '', 'Admin/upload', '上传文件', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (29, '', 'Admin/addEdit', '添加/修改管理员', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (30, '', 'Admin/delete', '删除管理员', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (31, '', 'Admin/isUse', '禁用/解禁管理员', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (32, '', 'Log/index', '日志首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (33, '', 'Log/delete', '删除日志', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (34, '', 'Role/index', '角色首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (35, '', 'Role/addEdit', '添加/修改角色', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (36, '', 'Role/isUse', '禁用/解禁角色', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (37, '', 'Role/delete', '删除角色', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (38, '', 'Rule/index', '规则首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (39, '', 'Rule/addEdit', '添加/修改规则', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (40, '', 'Rule/delete', '删除规则', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (41, '', 'Rule/icon', '读取Icon图标', 0, 0, 0, '', 1594289783, 1594289783);
INSERT INTO `tp_rule` VALUES (42, '', 'Rule/authTree', '获取权限树', 0, 0, 0, '', 1594289892, 1594289892);
INSERT INTO `tp_rule` VALUES (43, 'fa-home', 'Admin/account', '管理员账号管理', 1, 0, 0, '', 1594289907, 1594289907);
INSERT INTO `tp_rule` VALUES (44, '', 'Profile/index', '个人信息', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (45, 'fa-bandcamp', 'Message/index', '消息管理首页', 1, 0, 0, '', 1594449155, 1594449155);
INSERT INTO `tp_rule` VALUES (46, '', 'Message/info', '消息详情', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (47, '', 'Message/receiver', '接收者列表', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (48, '', 'Message/send', '批量发送消息', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (49, '', 'Admin/lang', '多语言选择', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (50, '', 'Notice/index', '公告管理首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (51, '', 'Notice/info', '公告详情', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (52, '', 'Notice/send', '发布公告', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (53, '', 'Equipment/realtimeStatus', '查看设备实时状态', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (54, '', 'Equipment/positionalInformation', '查看设备位置信息', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (55, '', 'Equipment/map', '位置信息的地图', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (56, '', 'EquipmentRun/index', '查看设备运行历史', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (57, '', 'EquipmentRun/export', '导出运行历史', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (58, '', 'EquipmentFault/index', '查看设备故障历史', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (59, '', 'EquipmentFault/export', '导出故障历史', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (60, '', 'EquipmentUpgrade/index', '查看设备升级历史', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (61, '', 'EquipmentUpgrade/export', '导出升级历史', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (62, '', 'Equipment/checkEquipmentDetailPages', '查看设备详情页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (63, '', 'Equipment/getChartData', '获取设备发电量的图表数据', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (64, '', 'EquipmentRepair/setFieldValue', '修改报修字段值', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (65, '', 'EquipmentRepair/solution', '报修解决方案', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (66, '', 'Region/index', '地区首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (67, '', 'Region/addEdit', '添加/修改地区', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (68, '', 'Region/delete', '删除地区', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (69, '', 'Equipment/addEdit', '添加/编辑设备', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (70, '', 'Item/index', '项目首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (71, '', 'Item/addEdit', '添加/修改项目', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (72, '', 'Item/delete', '删除项目', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (73, '', 'Item/equipmentLists', '项目的设备列表', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (74, '', 'Item/equipmentJoinItem', '设备加入项目', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (75, '', 'Equipment/delete', '删除设备', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (76, '', 'User/addEdit', '添加/修改用户', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (77, '', 'User/equipmentLists', '用户的设备列表', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (78, '', 'User/equipmentJoinUser', '设备加入用户', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (79, '', 'User/upload', '用户上传', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (80, '', 'Agent/equipmentLists', '代理商的设备列表', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (81, 'fa-font-awesome', 'Agent/equipmentJoinAgent', '设备加入代理商', 1, 0, 0, '', 0, 1607483905);
INSERT INTO `tp_rule` VALUES (82, '', 'UpgradePackage/index', '升级包首页', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (83, '', 'UpgradePackage/addEdit', '添加/修改升级包', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (84, '', 'UpgradePackage/delete', '删除升级包', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (85, '', 'UpgradePackage/upload', '上传升级包', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (86, '', 'User/export', '导出用户', 1, 0, 0, '', 0, 0);
INSERT INTO `tp_rule` VALUES (87, '', 'Equipment/alarmParam', '报警参数', 1, 0, 0, '', 0, 0);

-- ----------------------------
-- Table structure for tp_system_setup
-- ----------------------------
DROP TABLE IF EXISTS `tp_system_setup`;
CREATE TABLE `tp_system_setup`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户表id',
  `battery_voltage` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '电池电压',
  `battery_current` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '电池电流',
  `battery_soc` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '电池SOC',
  `battery_soh` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '电池SOH',
  `battery_temperature` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '电池温度',
  `battery_cycle_number` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '电池循环次数',
  `pv1_voltage` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'PV1电压',
  `pv1_current` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'PV1电流',
  `pv2_voltage` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'PV2电压',
  `pv2_current` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'PV2电流',
  `grid_voltage` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '电网电压',
  `grid_current` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '电网电流',
  `load_voltage` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '负载电压',
  `load_current` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '负载电流',
  `inverter_voltage` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '逆变电压',
  `inverter_current` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '逆变电流',
  `grid_frequency` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '电网频率',
  `power_factor` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '功率因数',
  `grounding_resistance` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接地电阻',
  `leakage_current` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '漏电流',
  `average_input_power` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '平均输入功率',
  `average_output_power` decimal(10, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '平均输出功率',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统设置表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_system_setup
-- ----------------------------
INSERT INTO `tp_system_setup` VALUES (12, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1615782174, 1615782174);

-- ----------------------------
-- Table structure for tp_upgrade_package
-- ----------------------------
DROP TABLE IF EXISTS `tp_upgrade_package`;
CREATE TABLE `tp_upgrade_package`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型：1app，2pad',
  `version` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '版本号',
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '路径',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '升级包表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_upgrade_package
-- ----------------------------
INSERT INTO `tp_upgrade_package` VALUES (2, 1, '1.0.0', '/upload/2021-01-15/6000e8654f7e9.apk', 1610672238, 1610672238);
INSERT INTO `tp_upgrade_package` VALUES (3, 1, '1.0.1', '/upload/2021-01-15/6000f4d67e742.apk', 1610675441, 1610675441);

-- ----------------------------
-- Table structure for tp_user
-- ----------------------------
DROP TABLE IF EXISTS `tp_user`;
CREATE TABLE `tp_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员表id',
  `agent_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '代理商表id',
  `nickname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '昵称，用于登录的账号名称',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码，用于登录的账号密码',
  `socket_sid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'socket连接标识',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `identity_card` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '身份证号',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别：0保密，1男，2女',
  `avatar` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `province` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省',
  `city` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '市',
  `area` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区',
  `address` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `lang` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'zh-cn' COMMENT '语言类型',
  `token` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用于应用授权 类似于session_id',
  `token_expire_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'token过期时间',
  `last_login_ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `is_use` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用：0否，1是',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_user
-- ----------------------------
INSERT INTO `tp_user` VALUES (12, 1, 0, '精工技术陈兵', '11ce2b1bed11f9e64aa86f728f2824f9', '', '', '', '', 'tec53@goldencell.biz', 0, '/upload/2021-03-15/604ee115c83eb.png', 0, 0, 0, '', 'zh-cn', '59ed2f92626ccd760c08df4e02888f06', 1616231196, '117.136.95.66', 1616144235, 1, 1615782174, 1616144830);

SET FOREIGN_KEY_CHECKS = 1;
