/*
 Navicat Premium Data Transfer

 Source Server         : 本机
 Source Server Type    : MySQL
 Source Server Version : 50710
 Source Host           : localhost
 Source Database       : static_manage

 Target Server Type    : MySQL
 Target Server Version : 50710
 File Encoding         : utf-8

 Date: 12/27/2019 15:45:41 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `file_version`
-- ----------------------------
DROP TABLE IF EXISTS `file_version`;
CREATE TABLE `file_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `dev_model` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开发模式，开发模式下css和js文件直接引入源文件，0为非开发模式，1为开发模式',
  `rename` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0上传后保持原文件名，1为生成新的文件名',
  `compress` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否压缩文件，针对CSS和JS文件才有用',
  `project_key` varchar(100) NOT NULL COMMENT '所属系统名称',
  `source_path` varchar(500) NOT NULL COMMENT '从项目下的static目录开始，源文件的磁盘路径',
  `version_path` varchar(500) NOT NULL COMMENT '版本文件的访问URL',
  `sinclude_path` varchar(500) DEFAULT '' COMMENT '引入shtml的路径',
  `ctime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1193 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `static_project`
-- ----------------------------
DROP TABLE IF EXISTS `static_project`;
CREATE TABLE `static_project` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_key` varchar(20) NOT NULL DEFAULT '' COMMENT '项目键名，仅由字母、数字、下划线组成',
  `project_name` varchar(40) NOT NULL DEFAULT '' COMMENT '项目名称',
  `description` varchar(200) NOT NULL DEFAULT '' COMMENT '描述',
  `static_path` varchar(500) NOT NULL DEFAULT '' COMMENT '存放静态文件的目录路径',
  `shtml_path` varchar(500) NOT NULL DEFAULT '' COMMENT '生成的shtml文件存放的目录',
  `ctime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `projectKey` (`project_key`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

SET FOREIGN_KEY_CHECKS = 1;
