create database shop6 charset=utf8;

use shop6;

-- 品牌
create table kang_brand
(
  id int unsigned auto_increment,
  title varchar(32) not null default '' comment '名称',
  logo varchar(255) not null default '' comment 'LOGO',
  site varchar(255) not null default '' comment '官网',
  sort_number int not null default 0 comment '排序',

  created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',
  primary key (id),
  key (title),
  key (sort_number)
) charset=utf8 comment = '品牌';


-- 管理员
create table kang_admin
(
  id int unsigned auto_increment,
  username varchar(32) not null default '' comment '管理员',
  password varchar(64) not null default '' comment '密码',
  salt varchar(12) not null default '' comment '盐',

  created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',

  primary key (id),
  index (username),
  index (password)
) charset=utf8 comment='管理员';

insert into kang_admin values (null, 'han', md5(concat('hellokang', '9a82')), '9a82', unix_timestamp(), unix_timestamp());
insert into kang_admin values (null, 'zhong', md5(concat('hellokang', '18e6')), '18e6', unix_timestamp(), unix_timestamp());
insert into kang_admin values (null, 'kang', md5(concat('hellokang', '9a82')), '9a82', unix_timestamp(), unix_timestamp());

-- 角色表
create table kang_role
(
  id int unsigned auto_increment,
  title varchar(32) not null default '' comment '角色名称',
  remark varchar(255) not null default '' comment '备注',

  created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',
  primary key (id)
  ) charset=utf8 comment='角色';
alter table kang_role add column is_super boolean not null default 0 comment '是否为超级管理员' after remark; -- boolean eq tinyint(1)


-- 角色管理员关联
create table kang_role_admin
(
  id int UNsigned auto_increment,

  role_id int unsigned not null default 0 comment '角色',
  admin_id int unsigned not null default 0 comment '管理员',

  created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',

  primary key (id),
  unique key (role_id, admin_id)
) charset=utf8 comment '角色管理员关联';

-- 动作表
create table kang_action
(
  id int unsigned auto_increment,
--   action varchar(32) not null default '' comment '动作',
--   controller varchar(32) not null default '' comment '控制器',
--   module varchar(32) not null default '' comment '模块',
  node varchar(32) not null default '' comment '节点', -- 模块, 控制器, 动作
  parent_id int unsigned not null default 0 comment '上级节点',
  level tinyint unsigned not null default 0 comment '节点级别', -- 模块1, 控制器2, 动作3
  title varchar(32) not null default '' comment '描述',

  created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',

  primary key (id),
  key (parent_id)
  ) charset=utf8 comment='动作';

-- 角色与动作关联
create table kang_role_action
(
  id int unsigned auto_increment,
  role_id int unsigned not null default 0 comment '角色',
  action_id int unsigned not null default 0 comment '动作',
  created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',

  primary key (id),
  unique key (role_id, action_id)
) charset=utf8 comment='角色与动作关联';


-- 商品分类表
drop table if exists kang_category;
create table kang_category (
	id int unsigned auto_increment comment 'ID',
	title varchar(32) not null default '' comment '分类',
	parent_id int unsigned not null default 0 comment '上级分类',
	sort_number int not null default 0 COMMENT '排序',
	image varchar(255) not null default '' comment '图片', -- 分类图片
	image_thumb varchar(255) not null default '' comment '缩略图', -- 分类图片缩略图
	is_used boolean not null default 1 comment '启用', -- tinyint(1)
	-- SEO优化
	meta_title varchar(255) not null default '' comment 'SEO标题',
	meta_keywords varchar(255) not null default '' comment 'SEO关键字',
	meta_description varchar(1024) not null default '' comment 'SEO描述',
	created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',
	primary key (id),
	index (parent_id),
	index (sort_number)
) charset=utf8 comment='分类';

-- 商品表
drop table if exists kang_goods;
create table kang_goods (
	id int unsigned auto_increment comment 'ID',
	upc varchar(255) not null default '' comment '通用代码', -- 通用商品代码
	title varchar(64) not null default '' comment '名称',
	image varchar(255) not null default '' comment '图像',
	image_thumb varchar(255) not null default '' comment '缩略图',
	sku_id int unsigned not null default 0 comment '库存单位', -- 库存单位
	price decimal(10, 2) not null default 0.0 comment '售价',
	tax_id int unsigned not null default 0 comment '税类型', -- 税类型ID
	quantity int unsigned not null default 0 comment '库存', -- 库存
	minimum int unsigned not null default 1 comment '最少起售', -- 最小起订数量
	is_subtract tinyint not null default 1 comment '扣减库存', -- 是否减少库存
	stock_status_id int unsigned not null default 0 comment '库存状态', -- 库存状态ID
	is_shipping tinyint not null default 1 comment '配送支持', -- 是否允许配送
	date_available timestamp not null default '0000-00-00 00:00:00' comment '起售时间', -- 供货日期
	length int unsigned not null default 0 comment '长',
	width int unsigned not null default 0 comment '宽',
	height int unsigned not null default 0 comment '高',
	length_unit_id int unsigned not null default 0 comment '长度单位', -- 长度单位
	weight int unsigned not null default 0 comment '重量',
	weight_unit_id int unsigned not null default 0 comment '重量单位', -- 重量的单位
	is_on_sale tinyint not null default 1 comment '上架', -- 是否可用
	sort_number int not null default 0 comment '排序', -- 排序
	description text comment '描述', -- 商品描述
	is_deleted tinyint not null default 0 comment '是否被删除', -- 是否被删除

	-- SEO优化
	meta_title varchar(255) not null default '' comment 'SEO标题',
	meta_keywords varchar(255) not null default '' comment 'SEO关键字',
	meta_description varchar(1024) not null default '' comment 'SEO描述',
	brand_id int unsigned not null default 0 comment '品牌', -- 所属品牌ID
	created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',
	primary key (id),
	index (brand_id),
	index (sku_id),
	index (tax_id),
	index (stock_status_id),
	index (length_unit_id),
	index (weight_unit_id),
	index (sort_number),
	index (title),
	index (price),
	unique key (upc)
) charset=utf8 comment '商品';

-- 税类型
drop table if exists kang_tax;
create table kang_tax (
	id int unsigned auto_increment comment 'ID',
	title varchar(32) not null default '' comment '税类型',
	created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',
	primary key (id)
) charset=utf8 comment '税类型';
-- 参考测试数据
insert into kang_tax values (1, '免税产品', unix_timestamp(), unix_timestamp());
insert into kang_tax values (2, '缴税产品', unix_timestamp(), unix_timestamp());
insert into kang_tax values (3, '反税产品', unix_timestamp(), unix_timestamp());


-- 库存单位
drop table if exists kang_stock_unit;
create table kang_stock_unit (
	id int unsigned auto_increment comment 'ID',
	title varchar(32) not null default '' comment '库存单位',
	created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',
	primary key (id)
) charset=utf8 comment '库存单位';
-- 参考测试数据
insert into kang_stock_unit values (1, '部', unix_timestamp(), unix_timestamp());
insert into kang_stock_unit values (2, '台', unix_timestamp(), unix_timestamp());
insert into kang_stock_unit values (3, '只', unix_timestamp(), unix_timestamp());
insert into kang_stock_unit values (4, '条', unix_timestamp(), unix_timestamp());
insert into kang_stock_unit values (5, '头', unix_timestamp(), unix_timestamp());


-- 库存状态
drop table if exists kang_stock_status;
create table kang_stock_status (
	id int unsigned auto_increment comment 'ID',
	title varchar(32) not null default '' comment '库存状态',
	created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',
	primary key (id)
) charset=utf8 comment '库存状态';
-- 参考测试数据
insert into kang_stock_status values (1, '库存充足', unix_timestamp(), unix_timestamp());
insert into kang_stock_status values (2, '脱销', unix_timestamp(), unix_timestamp());
insert into kang_stock_status values (3, '预定', unix_timestamp(), unix_timestamp());
insert into kang_stock_status values (4, '1至3周销售', unix_timestamp(), unix_timestamp());
insert into kang_stock_status values (5, '1至3天销售', unix_timestamp(), unix_timestamp());


-- 长度单位
drop table if exists kang_length_unit;
create table kang_length_unit (
	id int unsigned auto_increment comment 'ID',
	title varchar(32) not null default '' comment '长度单位',
	created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',
	primary key (id)
) charset=utf8 comment '长度单位';
-- 参考测试数据
insert into kang_length_unit values (1, '厘米', unix_timestamp(), unix_timestamp());
insert into kang_length_unit values (2, '毫米', unix_timestamp(), unix_timestamp());
insert into kang_length_unit values (3, '米', unix_timestamp(), unix_timestamp());
insert into kang_length_unit values (4, '千米', unix_timestamp(), unix_timestamp());
insert into kang_length_unit values (5, '英寸', unix_timestamp(), unix_timestamp());

-- 重量单位
drop table if exists kang_weight_unit;
create table kang_weight_unit (
	id int unsigned auto_increment comment 'ID',
	title varchar(32) not null default '' comment '重量单位',
	created_at int not null default 0 comment '创建时间',
  updated_at int not null default 0 comment '修改时间',
	primary key (id)
) charset=utf8 comment '重量单位';
-- 参考测试数据
insert into kang_weight_unit values (1, '克', unix_timestamp(), unix_timestamp());
insert into kang_weight_unit values (2, '千克', unix_timestamp(), unix_timestamp());
insert into kang_weight_unit values (3, '克拉', unix_timestamp(), unix_timestamp());
insert into kang_weight_unit values (4, '市斤', unix_timestamp(), unix_timestamp());
insert into kang_weight_unit values (5, '吨', unix_timestamp(), unix_timestamp());
insert into kang_weight_unit values (6, '磅', unix_timestamp(), unix_timestamp());


-- 商品相册图片
drop table if exists kang_image;
create table kang_image
(
	id int unsigned auto_increment comment 'ID',
	goods_id int unsigned not null default 0 comment '所属商品',
	image varchar(255) not null default '' comment '图像',
	image_small varchar(255) not null default '' comment '小图',
	image_medium varchar(255) not null default '' comment '中图',
	image_big varchar(255) not null default '' comment '大图',
	description varchar(255) not null DEFAULT  '' comment '描述',
	sort_number int not null default 0 comment '排序',
	created_at int not null default 0 comment '创建时间',
	updated_at int not null default 0 comment '修改时间',
	primary key (id),
	key (goods_id),
	key (sort_number)
) charset=utf8 COMMENT='相册图像';

-- 商品属性类型
drop table if exists kang_type;
create table kang_type
(
	id int unsigned AUTO_INCREMENT comment 'ID',
	created_at int not null default 0 comment '创建时间',
	updated_at int not null default 0 comment '修改时间',
	title varchar(32) not null default '' comment '类型',
	primary key (id),
	key (title)
) charset=utf8 COMMENT='类型';

-- 属性表
drop table if exists kang_attribute;
create table kang_attribute
(
	id int unsigned AUTO_INCREMENT comment 'ID',
	created_at int not null default 0 comment '创建时间',
	updated_at int not null default 0 comment '修改时间',
	sort_number int not null default 0 comment '排序',
	type_id int unsigned not null default 0 comment '类型',
	input_id int unsigned not null default 0 comment '输入元素',
	title varchar(32) not null default '' comment '属性',
	primary key (id),
	key (type_id),
	key (input_id),
	key (sort_number)
) charset=utf8 comment='属性';

-- 输入类型
drop table if EXISTS kang_input;
create table kang_input
(
	id int unsigned AUTO_INCREMENT comment 'ID',
	created_at int not null default 0 comment '创建时间',
	updated_at int not null default 0 comment '修改时间',
	title varchar(32) not null default 0 comment '类型', -- 用于展示: 选项, 输入
	`key` varchar(32) not null default 0 comment 'KEY', -- 用于判断: select-multi, text
	primary key (id),
	unique key (`key`)
) charset=utf8 comment='输入类型';

-- 选项表
drop table if exists kang_option;
create table kang_option
(
	id int unsigned AUTO_INCREMENT comment 'ID',
	created_at int not null default 0 comment '创建时间',
	updated_at int not null default 0 comment '修改时间',

	title varchar(32) not null default '' comment '选项',
	attribute_id int unsigned not null default 0 comment '所属的属性',
	PRIMARY KEY (id),
	key (attribute_id)
) charset=utf8 comment='选项';

-- 商品与属性关联
drop table if exists kang_goods_attribute;
create table kang_goods_attribute
(
	id int unsigned AUTO_INCREMENT comment 'ID',
	created_at int not null default 0 comment '创建时间',
	updated_at int not null default 0 comment '修改时间',

	goods_id int not null default 0 comment '商品',
	attribute_id int not null default 0 comment '属性',
	value varchar(255) not null default '' comment '值',

	primary key (id),
	key (goods_id), -- index (goods_id) constraint
	key (attribute_id)
) charset=utf8 comment='商品属性关联';

-- 增加商品和属性类型的关联
alter TABLE  kang_goods add column type_id int unsigned not null default 0 comment '属性类型' after brand_id;

-- 商品属性与选项关联表
drop table if exists kang_goods_attribute_option;
create table kang_goods_attribute_option
(
	id int unsigned AUTO_INCREMENT comment 'ID',
	created_at int not null default 0 comment '创建时间',
	updated_at int not null default 0 comment '修改时间',

	goods_attribute_id int unsigned not null default 0 comment '商品属性标志',
	option_id int UNSIGNED not null DEFAULT 0 comment '选项',

	primary key (id),
	key (goods_attribute_id),
	key (option_id)
) CHARset=utf8 comment='商品属性与选项';