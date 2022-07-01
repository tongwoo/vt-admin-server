/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50734
 Source Host           : localhost:3306
 Source Schema         : vt-admin

 Target Server Type    : MySQL
 Target Server Version : 50734
 File Encoding         : 65001

 Date: 30/06/2022 09:47:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for attachment
-- ----------------------------
DROP TABLE IF EXISTS `attachment`;
CREATE TABLE `attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL COMMENT '名称',
  `filename` varchar(256) NOT NULL COMMENT '文件名',
  `extension` varchar(32) NOT NULL COMMENT '扩展名',
  `path` varchar(512) NOT NULL COMMENT '路径',
  `size` bigint(20) unsigned NOT NULL COMMENT '大小',
  `time` datetime NOT NULL COMMENT '时间',
  `hash` varchar(32) DEFAULT NULL COMMENT '哈希',
  `is_valid` tinyint(1) unsigned DEFAULT '0' COMMENT '是否有效',
  PRIMARY KEY (`id`),
  KEY `hash_key` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件';

-- ----------------------------
-- Records of attachment
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for authorization
-- ----------------------------
DROP TABLE IF EXISTS `authorization`;
CREATE TABLE `authorization` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '用户',
  `value` varchar(128) NOT NULL COMMENT '授权值',
  `expires_time` datetime NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户授权';

-- ----------------------------
-- Records of authorization
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for permission
-- ----------------------------
DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL COMMENT '父权限',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '权限名称',
  `description` varchar(32) NOT NULL DEFAULT '' COMMENT '权限描述',
  `rule_name` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COMMENT='权限';

-- ----------------------------
-- Records of permission
-- ----------------------------
BEGIN;
INSERT INTO `permission` VALUES (114, 0, 'role', '角色管理', '');
INSERT INTO `permission` VALUES (115, 114, 'role_create', '创建角色', '');
INSERT INTO `permission` VALUES (116, 114, 'role_read', '查看角色', '');
INSERT INTO `permission` VALUES (117, 114, 'role_update', '修改角色', '');
INSERT INTO `permission` VALUES (118, 114, 'role_delete', '删除角色', '');
INSERT INTO `permission` VALUES (119, 0, 'user', '用户管理', '');
INSERT INTO `permission` VALUES (120, 119, 'user_create', '创建用户', '');
INSERT INTO `permission` VALUES (121, 119, 'user_read', '查看用户', '');
INSERT INTO `permission` VALUES (122, 119, 'user_update', '修改用户', '');
INSERT INTO `permission` VALUES (123, 119, 'user_delete', '删除用户', '');
INSERT INTO `permission` VALUES (124, 0, 'permission', '权限管理', '');
INSERT INTO `permission` VALUES (125, 124, 'permission_create', '创建权限', '');
INSERT INTO `permission` VALUES (126, 124, 'permission_read', '查看权限', '');
INSERT INTO `permission` VALUES (127, 124, 'permission_update', '修改权限', '');
INSERT INTO `permission` VALUES (128, 124, 'permission_delete', '删除权限', '');
INSERT INTO `permission` VALUES (129, 0, 'route', '路由管理', '');
INSERT INTO `permission` VALUES (130, 129, 'route_create', '创建路由', '');
INSERT INTO `permission` VALUES (131, 129, 'route_read', '查看路由', '');
INSERT INTO `permission` VALUES (132, 129, 'route_update', '修改路由', '');
INSERT INTO `permission` VALUES (133, 129, 'route_delete', '删除路由', '');
INSERT INTO `permission` VALUES (134, 131, 'sdgdf', 'fgf', '');
INSERT INTO `permission` VALUES (135, 134, 'sdgdf_create', '创建fgf', '');
INSERT INTO `permission` VALUES (136, 134, 'sdgdf_read', '查看fgf', '');
INSERT INTO `permission` VALUES (137, 134, 'sdgdf_update', '修改fgf', '');
INSERT INTO `permission` VALUES (138, 134, 'sdgdf_delete', '删除fgf', '');
COMMIT;

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '角色名称',
  `description` varchar(32) NOT NULL COMMENT '角色描述',
  `rule_name` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `is_built_in` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否内置',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COMMENT='角色';

-- ----------------------------
-- Records of role
-- ----------------------------
BEGIN;
INSERT INTO `role` VALUES (22, 'admin', '管理员', '', 0);
INSERT INTO `role` VALUES (23, 'model-builder', '建模师', '', 0);
INSERT INTO `role` VALUES (24, 'gm', '游戏管理员', '', 0);
INSERT INTO `role` VALUES (25, 'service', '客服', '', 0);
INSERT INTO `role` VALUES (26, 'saler', '销售', '', 0);
INSERT INTO `role` VALUES (27, 'heshang', '扫地僧', '', 0);
COMMIT;

-- ----------------------------
-- Table structure for role_permission
-- ----------------------------
DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE `role_permission` (
  `role_id` int(11) unsigned NOT NULL COMMENT '所属角色',
  `permission_id` int(11) unsigned NOT NULL COMMENT '所属权限',
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_id` (`role_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色权限';

-- ----------------------------
-- Records of role_permission
-- ----------------------------
BEGIN;
INSERT INTO `role_permission` VALUES (25, 129);
INSERT INTO `role_permission` VALUES (25, 131);
INSERT INTO `role_permission` VALUES (25, 134);
INSERT INTO `role_permission` VALUES (25, 135);
INSERT INTO `role_permission` VALUES (25, 136);
INSERT INTO `role_permission` VALUES (25, 137);
INSERT INTO `role_permission` VALUES (25, 138);
COMMIT;

-- ----------------------------
-- Table structure for route
-- ----------------------------
DROP TABLE IF EXISTS `route`;
CREATE TABLE `route` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(10) unsigned DEFAULT NULL COMMENT '权限',
  `name` varchar(128) DEFAULT NULL COMMENT '名称',
  `path` varchar(256) DEFAULT NULL COMMENT '路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COMMENT='路由';

-- ----------------------------
-- Records of route
-- ----------------------------
BEGIN;
INSERT INTO `route` VALUES (1, NULL, '权限所有记录', '/permission/items');
INSERT INTO `route` VALUES (2, NULL, '权限分页记录', '/permission/page-items');
INSERT INTO `route` VALUES (3, NULL, '权限详情', '/permission/detail');
INSERT INTO `route` VALUES (4, NULL, '创建权限', '/permission/create');
INSERT INTO `route` VALUES (5, NULL, '更新权限', '/permission/update');
INSERT INTO `route` VALUES (6, NULL, '删除权限', '/permission/delete');
INSERT INTO `route` VALUES (7, NULL, '权限树', '/permission/tree');
INSERT INTO `route` VALUES (8, NULL, '规则列表', '/permission/rules');
INSERT INTO `route` VALUES (9, NULL, '用户所有记录', '/user/items');
INSERT INTO `route` VALUES (10, NULL, '用户分页记录', '/user/page-items');
INSERT INTO `route` VALUES (11, NULL, '用户详情', '/user/detail');
INSERT INTO `route` VALUES (12, NULL, '创建用户', '/user/create');
INSERT INTO `route` VALUES (13, NULL, '更新用户', '/user/update');
INSERT INTO `route` VALUES (14, NULL, '删除用户', '/user/delete');
INSERT INTO `route` VALUES (15, NULL, '账户登录', '/login/index');
INSERT INTO `route` VALUES (16, NULL, '退出登录', '/login/exit');
INSERT INTO `route` VALUES (17, NULL, '角色所有记录', '/role/items');
INSERT INTO `route` VALUES (18, NULL, '角色分页记录', '/role/page-items');
INSERT INTO `route` VALUES (19, NULL, '角色详情', '/role/detail');
INSERT INTO `route` VALUES (20, NULL, '创建角色', '/role/create');
INSERT INTO `route` VALUES (21, NULL, '更新角色', '/role/update');
INSERT INTO `route` VALUES (22, NULL, '删除角色', '/role/delete');
INSERT INTO `route` VALUES (23, NULL, '角色下的权限列表', '/role/permissions');
INSERT INTO `route` VALUES (24, NULL, '绑定权限', '/role/bind');
INSERT INTO `route` VALUES (25, NULL, '路由所有记录', '/route/items');
INSERT INTO `route` VALUES (26, NULL, '路由分页记录', '/route/page-items');
INSERT INTO `route` VALUES (27, NULL, '路由详情', '/route/detail');
INSERT INTO `route` VALUES (28, NULL, '创建路由', '/route/create');
INSERT INTO `route` VALUES (29, NULL, '更新路由', '/route/update');
INSERT INTO `route` VALUES (30, NULL, '删除路由', '/route/delete');
INSERT INTO `route` VALUES (31, NULL, '清空路由', '/route/truncate');
INSERT INTO `route` VALUES (32, NULL, '自动创建', '/route/generate');
INSERT INTO `route` VALUES (33, NULL, '未知', '/test/download');
INSERT INTO `route` VALUES (34, NULL, '上传文件', '/upload/common');
INSERT INTO `route` VALUES (35, NULL, '上传头像', '/upload/avatar');
INSERT INTO `route` VALUES (36, NULL, '编辑器文件上传', '/upload/editor');
INSERT INTO `route` VALUES (37, NULL, '附件所有记录', '/attachment/items');
INSERT INTO `route` VALUES (38, NULL, '附件分页记录', '/attachment/page-items');
INSERT INTO `route` VALUES (39, NULL, '附件详情', '/attachment/detail');
INSERT INTO `route` VALUES (40, NULL, '创建附件', '/attachment/create');
INSERT INTO `route` VALUES (41, NULL, '更新附件', '/attachment/update');
INSERT INTO `route` VALUES (42, NULL, '删除附件', '/attachment/delete');
INSERT INTO `route` VALUES (43, NULL, '默认Action', '/site/index');
INSERT INTO `route` VALUES (44, NULL, '错误处理', '/site/error');
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码',
  `name` varchar(32) NOT NULL COMMENT '姓名',
  `avatar` varchar(100) DEFAULT '' COMMENT '头像',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：启用=enabled=1,禁用=disabled=0',
  `login_time` datetime DEFAULT NULL COMMENT '上次登录时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_name` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='用户';

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (3, 'admin', '$2y$13$g.w/gY.majgjjwFnunxZAuX91dcbfiOEPY7yaiNyzrABL1FRubdmC', '超级管理员', '', 1, NULL);
INSERT INTO `user` VALUES (5, 'xiaowu', '$2y$13$cU7PXP.zUasXOyXK9eflEOKCsOK53O03QW6Y8sfp9.gZxDB0ebjE2', 'PHP工程师', '', 1, NULL);
INSERT INTO `user` VALUES (6, 'frontender', '$2y$13$Yan144jBA7cErkWDl900ZO79Bb4mnu8BGg2bF7Vh3XY2e9ITs4coG', '前端工程师', '', 1, NULL);
INSERT INTO `user` VALUES (7, 'Java', '$2y$13$j42MA1umnRPyfclwTDf7.ujESYD1tQXnkJtW8VQORfQqBjouctrKe', 'Java工程师', '', 1, NULL);
COMMIT;

-- ----------------------------
-- Table structure for user_role
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `role_id` int(10) unsigned NOT NULL COMMENT '所属角色',
  `user_id` int(10) unsigned NOT NULL COMMENT '所属用户',
  PRIMARY KEY (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户角色';

-- ----------------------------
-- Records of user_role
-- ----------------------------
BEGIN;
INSERT INTO `user_role` VALUES (22, 3);
INSERT INTO `user_role` VALUES (23, 5);
INSERT INTO `user_role` VALUES (23, 6);
INSERT INTO `user_role` VALUES (24, 5);
INSERT INTO `user_role` VALUES (24, 7);
INSERT INTO `user_role` VALUES (26, 5);
INSERT INTO `user_role` VALUES (27, 5);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
