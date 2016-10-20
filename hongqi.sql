/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50624
Source Host           : localhost:3306
Source Database       : hongqi

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-10-20 08:57:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `account_billing`
-- ----------------------------
DROP TABLE IF EXISTS `account_billing`;
CREATE TABLE `account_billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(255) DEFAULT NULL,
  `term` varchar(255) DEFAULT NULL COMMENT '收费学期',
  `student_taition` int(11) DEFAULT NULL COMMENT '学费',
  `student_data` int(11) DEFAULT NULL COMMENT '资料费',
  `student_accommodation` int(11) DEFAULT NULL COMMENT '住宿费',
  `student_meal` int(11) DEFAULT NULL COMMENT '包餐费',
  `student_insurance` int(11) DEFAULT NULL COMMENT '保险费',
  `create_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of account_billing
-- ----------------------------

-- ----------------------------
-- Table structure for `campus`
-- ----------------------------
DROP TABLE IF EXISTS `campus`;
CREATE TABLE `campus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campus_name` varchar(255) DEFAULT NULL COMMENT '校区名字',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of campus
-- ----------------------------
INSERT INTO `campus` VALUES ('1', '校本部');

-- ----------------------------
-- Table structure for `charge_billing`
-- ----------------------------
DROP TABLE IF EXISTS `charge_billing`;
CREATE TABLE `charge_billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_billing_id` int(11) DEFAULT NULL COMMENT '对应的账单',
  `type` varchar(255) DEFAULT NULL COMMENT '类别：收费，减免，退费',
  `charge_money` int(11) DEFAULT NULL COMMENT '每次收取的费用',
  `create_by` int(11) DEFAULT NULL COMMENT '操作人员也就是收费人员',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of charge_billing
-- ----------------------------

-- ----------------------------
-- Table structure for `charge_standard`
-- ----------------------------
DROP TABLE IF EXISTS `charge_standard`;
CREATE TABLE `charge_standard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_year` varchar(255) DEFAULT NULL COMMENT '学年',
  `school_term` varchar(255) DEFAULT NULL COMMENT '学期',
  `student_grade` varchar(255) DEFAULT NULL COMMENT '年级',
  `student_test` varchar(255) DEFAULT NULL COMMENT '是否实验班',
  `student_taition` int(11) DEFAULT NULL COMMENT '学生学费',
  `student_data` int(11) DEFAULT NULL COMMENT '学生资料费',
  `student_accommodation` int(11) DEFAULT NULL COMMENT '学生住宿费',
  `student_meal` int(11) DEFAULT NULL COMMENT '学生包餐费',
  `student_insurance` int(11) DEFAULT NULL COMMENT '学生保险费',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of charge_standard
-- ----------------------------
INSERT INTO `charge_standard` VALUES ('1', '2016', '春季学期', '小班', '正常班', '1000', '100', '500', '500', '50');
INSERT INTO `charge_standard` VALUES ('2', '2016', '春季学期', '中班', '正常班', '1000', '100', '500', '500', '50');
INSERT INTO `charge_standard` VALUES ('3', '2016', '春季学期', '大班', '正常班', '1000', '100', '500', '500', '50');
INSERT INTO `charge_standard` VALUES ('4', '2016', '春季学期', '一年级', '正常班', '1200', '150', '500', '800', '50');
INSERT INTO `charge_standard` VALUES ('5', '2016', '春季学期', '二年级', '正常班', '1200', '150', '500', '800', '50');
INSERT INTO `charge_standard` VALUES ('6', '2016', '春季学期', '三年级', '正常班', '1200', '150', '500', '800', '50');
INSERT INTO `charge_standard` VALUES ('7', '2016', '春季学期', '四年级', '正常班', '1200', '150', '500', '800', '50');
INSERT INTO `charge_standard` VALUES ('8', '2016', '春季学期', '五年级', '正常班', '1200', '150', '500', '800', '50');
INSERT INTO `charge_standard` VALUES ('9', '2016', '春季学期', '六年级', '正常班', '1200', '150', '500', '800', '50');
INSERT INTO `charge_standard` VALUES ('10', '2016', '春季学期', '七年级', '正常班', '1800', '200', '500', '1000', '50');
INSERT INTO `charge_standard` VALUES ('11', '2016', '春季学期', '八年级', '正常班', '1800', '200', '500', '1000', '50');
INSERT INTO `charge_standard` VALUES ('12', '2016', '春季学期', '九年级', '正常班', '1800', '200', '500', '1000', '50');
INSERT INTO `charge_standard` VALUES ('13', '2016', '春季学期', '二年级', '实验班', '1500', '200', '500', '800', '50');
INSERT INTO `charge_standard` VALUES ('14', '2016', '春季学期', '三年级', '实验班', '1500', '200', '500', '800', '50');

-- ----------------------------
-- Table structure for `department`
-- ----------------------------
DROP TABLE IF EXISTS `department`;
CREATE TABLE `department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(255) DEFAULT NULL COMMENT '学校学部：幼儿部，小学部，初中部',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of department
-- ----------------------------
INSERT INTO `department` VALUES ('1', '幼儿部', '2016-10-19 09:08:43');
INSERT INTO `department` VALUES ('2', '小学部', '2016-10-19 09:08:48');
INSERT INTO `department` VALUES ('3', '初中部', '2016-10-19 09:08:59');

-- ----------------------------
-- Table structure for `grade`
-- ----------------------------
DROP TABLE IF EXISTS `grade`;
CREATE TABLE `grade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) DEFAULT NULL COMMENT '学部ID',
  `student_grade` varchar(255) DEFAULT NULL COMMENT '年级',
  `student_test` varchar(255) DEFAULT NULL COMMENT '年级包含的类型：正常班，实验班',
  `create_by` varchar(255) DEFAULT NULL COMMENT '数据创建人工号',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of grade
-- ----------------------------
INSERT INTO `grade` VALUES ('1', '1', '小班', '正常班', '12121612', '2016-10-19 09:16:48');
INSERT INTO `grade` VALUES ('2', '1', '中班', '正常班', '12121612', '2016-10-19 09:16:49');
INSERT INTO `grade` VALUES ('3', '1', '大班', '正常班', '12121612', '2016-10-19 09:16:50');
INSERT INTO `grade` VALUES ('4', '2', '一年级', '正常班', '12121612', '2016-10-19 09:16:50');
INSERT INTO `grade` VALUES ('5', '2', '二年级', '正常班', '12121612', '2016-10-19 09:16:51');
INSERT INTO `grade` VALUES ('6', '2', '三年级', '正常班', '12121612', '2016-10-19 09:16:52');
INSERT INTO `grade` VALUES ('7', '2', '四年级', '正常班', '12121612', '2016-10-19 09:16:53');
INSERT INTO `grade` VALUES ('8', '2', '五年级', '正常班', '12121612', '2016-10-19 09:16:53');
INSERT INTO `grade` VALUES ('9', '2', '六年级', '正常班', '12121612', '2016-10-19 09:16:55');
INSERT INTO `grade` VALUES ('10', '3', '七年级', '正常班', '12121612', '2016-10-19 09:16:56');
INSERT INTO `grade` VALUES ('11', '3', '八年级', '正常班', '12121612', '2016-10-19 09:16:56');
INSERT INTO `grade` VALUES ('12', '3', '九年级', '正常班', '12121612', '2016-10-19 09:16:58');
INSERT INTO `grade` VALUES ('13', '2', '二年级', '实验班', '12121612', '2016-10-19 09:17:32');
INSERT INTO `grade` VALUES ('14', '2', '三年级', '实验班', '12121612', '2016-10-19 09:17:35');

-- ----------------------------
-- Table structure for `student`
-- ----------------------------
DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(255) DEFAULT NULL COMMENT '学生学号',
  `student_name` varchar(255) DEFAULT NULL COMMENT '学生姓名',
  `student_password` varchar(255) DEFAULT NULL COMMENT '学生密码',
  `student_nation` varchar(255) DEFAULT NULL COMMENT '学生民族',
  `student_sex` varchar(255) DEFAULT NULL COMMENT '学生性别',
  `student_birthdate` date DEFAULT NULL COMMENT '学生出生日期',
  `student_province` varchar(255) DEFAULT NULL COMMENT '学生所在省',
  `student_city` varchar(255) DEFAULT NULL COMMENT '学生所在市',
  `student_area` varchar(255) DEFAULT NULL COMMENT '学生所在县，乡',
  `student_address` varchar(255) DEFAULT NULL COMMENT '学生详细地址',
  `student_father` varchar(255) DEFAULT NULL COMMENT '学生父亲',
  `student_father_phone` varchar(255) DEFAULT NULL COMMENT '学生父亲联系方式',
  `student_mother` varchar(255) DEFAULT NULL COMMENT '学生母亲',
  `student_mother_phone` varchar(255) DEFAULT NULL COMMENT '学生母亲电话',
  `student_photo` varchar(255) DEFAULT NULL COMMENT '学生照片存储路径',
  `create_by` varchar(255) DEFAULT NULL COMMENT '此条数据创建者',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of student
-- ----------------------------
INSERT INTO `student` VALUES ('1', '12121672', '张三', '12121672', '汉族', '男', '1993-07-08', '河南', '周口市', '项城市', '永丰乡冯滩村', '张父', '18818211234', '张母', '18818212345', null, '12121612', '2016-10-18 17:41:23');
INSERT INTO `student` VALUES ('2', '12121673', '赵四', '12121673', '汉族', '女', '1994-07-08', '河南', '周口市', '项城市', '永丰乡小冯滩村', '赵父', '18818215678', '赵母', '18818218901', null, '12121612', '2016-10-18 18:45:09');
INSERT INTO `student` VALUES ('3', '12121675', '小五', '12121675', '汉族', '男', '1996-08-22', '河南', '周口市', '项城市', '永丰乡冯滩村', '小五父亲', '18818211234', '小五母亲', '18818212345', null, '12121612', '2016-10-19 16:48:15');
INSERT INTO `student` VALUES ('5', '12121676', '', '12121676', '', '男', '1993-07-08', '北京', '北京市', '东城区', '', '', '', '', '', './StudentPhoto/2016-10-19/12121676studentphoto.jpg', '12121612', '2016-10-19 19:58:58');

-- ----------------------------
-- Table structure for `student_file`
-- ----------------------------
DROP TABLE IF EXISTS `student_file`;
CREATE TABLE `student_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(255) DEFAULT NULL COMMENT '学生学号',
  `student_grade` varchar(255) DEFAULT NULL COMMENT '学生年级',
  `student_class` varchar(255) DEFAULT NULL COMMENT '学生班级',
  `student_test` varchar(255) DEFAULT NULL COMMENT '学生是否是实验班学生',
  `student_campus` varchar(255) DEFAULT NULL COMMENT '学生所在校区',
  `student_dorm_building` varchar(255) DEFAULT NULL COMMENT '学生宿舍楼',
  `student_dorm_number` varchar(255) DEFAULT NULL COMMENT '学生宿舍号',
  `student_accommodation` varchar(255) DEFAULT NULL COMMENT '学生是否住宿',
  `student_meal` varchar(255) DEFAULT NULL COMMENT '学生是否包餐',
  `student_data` varchar(255) DEFAULT NULL COMMENT '学生是否要资料费',
  `student_insurance` varchar(255) DEFAULT NULL COMMENT '学生是否要保险',
  `student_status` varchar(255) DEFAULT NULL COMMENT '学生在校状态：在校，退学，休学，转学',
  `create_by` varchar(255) DEFAULT NULL COMMENT '数据创建者',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of student_file
-- ----------------------------
INSERT INTO `student_file` VALUES ('1', '12121672', '七年级', '八班', '是', '本部', '校内A楼', '203', '是', '是', '是', '是', '休学', '12121612', '2016-10-19 16:49:11');
INSERT INTO `student_file` VALUES ('2', '12121673', '八年级', '八班', '否', '本部', '校内B楼', '204', '否', '是', '否', '是', '退学', '12121612', '2016-10-18 19:34:06');

-- ----------------------------
-- Table structure for `teacher`
-- ----------------------------
DROP TABLE IF EXISTS `teacher`;
CREATE TABLE `teacher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` varchar(255) DEFAULT NULL COMMENT '教师工号',
  `teacher_name` varchar(255) DEFAULT NULL COMMENT '教师姓名',
  `teacher_password` varchar(255) DEFAULT NULL COMMENT '教师密码',
  `teacher_phone` varchar(255) DEFAULT NULL COMMENT '教师手机号',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of teacher
-- ----------------------------
INSERT INTO `teacher` VALUES ('1', '12121612', '冯郭飞', '12345678', '18818217017', '2016-10-18 09:28:20');
