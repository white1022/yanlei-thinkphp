/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 80012
 Source Host           : 127.0.0.1:3306
 Source Schema         : yanlei

 Target Server Type    : MySQL
 Target Server Version : 80012
 File Encoding         : 65001

 Date: 08/01/2022 20:55:13
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
) ENGINE = InnoDB AUTO_INCREMENT = 44 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_access
-- ----------------------------
INSERT INTO `tp_access` VALUES (42, 2, 7, 1641460643, 1641460643);
INSERT INTO `tp_access` VALUES (43, 1, 1, 1641559227, 1641559227);

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
  `mobile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '电话',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `is_use` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否启用：1是，2否',
  `last_login_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `lang` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '语言类型',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_admin
-- ----------------------------
INSERT INTO `tp_admin` VALUES (1, 'admin', '/storage/20220107/bf7e979a2d8f33c408b7dc9307f3e887.jpeg', 'admin@qq.com', '$2y$10$Y8jZegVKGWsf7LFEi1BpIOARpONZ3oPevGtmubP3BX1Nd0qSCwnf2', '18396837961', '管理员', 1, '112.231.121.39', 1641634688, 'en-us', 1608013736, 1641634688);
INSERT INTO `tp_admin` VALUES (2, 'vWI7E22Y', '/storage/20220105/debc36d6aaf30aac61c7530a4005d35f.jpg', 'ceshi@qq.com', '$2y$10$Gqeo2/Vt1ZjBRsgGNsqYOuANEHxeKVmmEo598ufUYozuq7nRirrwS', '18396837963', '1111', 1, '223.104.191.175', 1641465909, 'en-us', 1608712304, 1641465909);

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
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '验证码表' ROW_FORMAT = COMPACT;

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
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '键',
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '值',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '字典表' ROW_FORMAT = COMPACT;

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
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '日志表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_log
-- ----------------------------
INSERT INTO `tp_log` VALUES (8, 1, 1, '登入', 1641609598, 1641609598);
INSERT INTO `tp_log` VALUES (9, 1, 1, '登出', 1641609636, 1641609636);
INSERT INTO `tp_log` VALUES (10, 1, 1, '登入', 1641609642, 1641609642);
INSERT INTO `tp_log` VALUES (11, 1, 1, '登出', 1641634679, 1641634679);
INSERT INTO `tp_log` VALUES (12, 1, 1, '登入', 1641634688, 1641634688);

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
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '公告表' ROW_FORMAT = COMPACT;

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
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '地区表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_region
-- ----------------------------
INSERT INTO `tp_region` VALUES (1, 0, '山东省', 1641630141, 1641630192);
INSERT INTO `tp_region` VALUES (2, 1, '济南市', 1641630206, 1641630206);
INSERT INTO `tp_region` VALUES (3, 2, '高新区', 1641630231, 1641630231);
INSERT INTO `tp_region` VALUES (4, 0, '上海市', 1641630256, 1641630282);
INSERT INTO `tp_region` VALUES (5, 4, '嘉定区', 1641630271, 1641630271);

-- ----------------------------
-- Table structure for tp_role
-- ----------------------------
DROP TABLE IF EXISTS `tp_role`;
CREATE TABLE `tp_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色拥有的规则id， 多个规则\",\"隔开',
  `is_use` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否启用：1是，2否',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '角色表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_role
-- ----------------------------
INSERT INTO `tp_role` VALUES (1, '管理员', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15', 1, 1608013736, 1641558529);
INSERT INTO `tp_role` VALUES (7, '测试', '1,8,9', 1, 1641459154, 1641467705);

-- ----------------------------
-- Table structure for tp_rule
-- ----------------------------
DROP TABLE IF EXISTS `tp_rule`;
CREATE TABLE `tp_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `jump` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '路由地址，默认按照 name 解析。一旦设置，将优先按照 jump 设定的路由跳转',
  `spread` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认展子菜单：1是，2否',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '规则表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_rule
-- ----------------------------
INSERT INTO `tp_rule` VALUES (1, 0, '', '权限管理', 'layui-icon-component', '', 2, 100, 1641288964, 1641525523);
INSERT INTO `tp_rule` VALUES (2, 1, '', '菜单管理', 'layui-icon-component', '', 2, 100, 1641288964, 0);
INSERT INTO `tp_rule` VALUES (3, 2, '', '菜单列表', 'layui-icon-component', '/rule/list', 2, 100, 1641288964, 1641357322);
INSERT INTO `tp_rule` VALUES (4, 1, '', '角色管理', 'layui-icon-component', '', 2, 100, 1641288964, 0);
INSERT INTO `tp_rule` VALUES (5, 4, '', '角色列表', 'layui-icon-component', '/role/list', 2, 100, 1641288964, 0);
INSERT INTO `tp_rule` VALUES (6, 1, '', '管理员管理', 'layui-icon-component', '', 2, 100, 1641288964, 0);
INSERT INTO `tp_rule` VALUES (7, 6, '', '管理员列表', 'layui-icon-component', '/admin/list', 2, 100, 1641288964, 1641357328);
INSERT INTO `tp_rule` VALUES (8, 0, '', '用户管理', 'layui-icon-component', '', 2, 100, 1641288964, 0);
INSERT INTO `tp_rule` VALUES (9, 8, '', '用户列表', 'layui-icon-component', '/user/list', 2, 100, 1641288964, 0);
INSERT INTO `tp_rule` VALUES (10, 0, '', '地区管理', 'layui-icon-component', '', 2, 100, 1641558140, 1641558140);
INSERT INTO `tp_rule` VALUES (11, 10, '', '地区列表', 'layui-icon-component', '/region/list', 2, 100, 1641558179, 1641558179);
INSERT INTO `tp_rule` VALUES (12, 0, '', '日志管理', 'layui-icon-component', '', 2, 100, 1641558293, 1641558293);
INSERT INTO `tp_rule` VALUES (13, 12, '', '日志列表', 'layui-icon-component', '/log/list', 2, 100, 1641558315, 1641558315);
INSERT INTO `tp_rule` VALUES (14, 0, '', '设置管理', 'layui-icon-component', '', 2, 100, 1641558360, 1641558360);
INSERT INTO `tp_rule` VALUES (15, 14, '', '系统设置', 'layui-icon-component', '/systemSetup/form', 2, 100, 1641558421, 1641558421);

-- ----------------------------
-- Table structure for tp_system_setup
-- ----------------------------
DROP TABLE IF EXISTS `tp_system_setup`;
CREATE TABLE `tp_system_setup`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `site_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '网站名称',
  `site_icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '网站图标',
  `site_copyright` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '网站版权',
  `site_detail` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '网站详情',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统设置表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_system_setup
-- ----------------------------
INSERT INTO `tp_system_setup` VALUES (1, 'layuiAdmin', '/storage/20220107/bf7e979a2d8f33c408b7dc9307f3e887.jpeg', '© All Rights Reserved', '<p><img src=\"http://www.yanlei-thinkphp.com/storage/20220108/d679046ba4a0efad38f54f1550807c5c.jpg\" alt=\"\"></p><p>123132113</p><p>呵呵呵</p>', 1639540816, 1641642456);

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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '升级包表' ROW_FORMAT = COMPACT;

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
  `nickname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '昵称，用于登录的账号名称',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码，用于登录的账号密码',
  `socket_sid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'socket连接标识',
  `avatar` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '电话',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `identity_card` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '身份证号',
  `sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别：0保密，1男，2女',
  `province` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省',
  `city` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '市',
  `area` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区',
  `address` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `lang` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'zh-cn' COMMENT '语言类型',
  `token` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '【废弃】用于应用授权 类似于session_id',
  `token_expire_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '【废弃】token过期时间',
  `last_login_ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `is_use` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否启用：1是，2否',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tp_user
-- ----------------------------
INSERT INTO `tp_user` VALUES (12, '精工技术陈兵', '11ce2b1bed11f9e64aa86f728f2824f9', '', '/storage/20220105/debc36d6aaf30aac61c7530a4005d35f.jpg', 'tec53@goldencell.biz', '', '', '', 0, 0, 0, 0, '', 'zh-cn', '59ed2f92626ccd760c08df4e02888f06', 1616231196, '117.136.95.66', 1616144235, 1, 1615782174, 1616144830);

SET FOREIGN_KEY_CHECKS = 1;
