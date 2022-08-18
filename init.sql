create table tbStatus (id int not null, fdName varchar(32), primary key (id) ) ;
insert into tbStatus VALUES (1, '未提交') ;
insert into tbStatus VALUES (2, '已提交未审核') ;
insert into tbStatus VALUES (4, '已审核未分派') ;
insert into tbStatus VALUES (8, '已分派未安排') ;
insert into tbStatus VALUES (16, '已安排未完成') ;
insert into tbStatus VALUES (32, '已完成未测试') ;
insert into tbStatus VALUES (64, '已测试未确认') ;
insert into tbStatus VALUES (128, '已确认未发布') ;
insert into tbStatus VALUES (256, '已发布完成') ;
insert into tbStatus VALUES (512, '已关闭') ;
create table tbModule (id int auto_increment, fdName varchar(32), primary key (id) ) ;
insert into tbModule values ( 0, '不确定' ) ;
insert into tbModule values ( 1, '计费' ) ;
insert into tbModule values ( 2, '帐务' ) ;
insert into tbModule values ( 3, '结算');
insert into tbModule values ( 4, '营业') ;
insert into tbModule values ( 5, '营收') ;
insert into tbModule values ( 6, '卡业务') ;
insert into tbModule values ( 7, '资源') ;
create table tbUser (id int auto_increment, fdEmail varchar(64), fdName varchar(32), fdPassword varchar(32), fdRole int, fdEmails varchar(128), primary key(id)) ;
create unique index nxModule on tbModule (id) ;
create unique index nxStatus on tbStatus (id) ;
create table tbNotice (fdIssueID int, fdUserID int, fdRoleID int) ;
create index nxNotice_Issue on tbNotice (fdIssueID) ;
create index nxNotice_User on tbNotice (fdUserID) ;
create index nxNotice_Role on tbNotice (fdRoleID) ;
create table tbComment (fdIssueID int, fdTime timestamp, fdUserID int, fdComment Text) ;
create index nxComment_User on tbComment (fdUserID) ;
create index nxComment_Issue on tbComment (fdIssueID) ;
create index nxComment_Time on tbComment (fdTime) ;
create table tbIssue (
  id int auto_increment,
  fdModuleID int,
  fdName varchar(255),
  fdDescription Text,
  fdPriorityID int,
  fdStatusID int,
  fdStep varchar(255),
  fdDeadline date,
  fdTypeID int,
  fdStart date,
  fdPush date,
  primary key (id)
) ;
create index nxIssue_Module on tbIssue (fdModuleID) ;
create index nxIssue_PriorityID on tbIssue (fdPriorityID) ;
create index nxIssue_StatusID on tbIssue (fdStatusID) ;
create index nxIssue_Handler on tbIssue (fdHandler) ;
create index nxIssue_Deadline on tbIssue (fdDeadline) ; 
create index nxIssue_Bug on tbIssue (fdBug) ; 
create table tbPriority (id int not null, fdName varchar(8), primary key (id)) ;
insert into tbPriority (id, fdName) VALUES (1, '1-最高') ;
insert into tbPriority (id, fdName) VALUES (2, '2-高') ;
insert into tbPriority (id, fdName) VALUES (3, '3-中') ;
insert into tbPriority (id, fdName) VALUES (4, '4-低') ;
insert into tbPriority (id, fdName) VALUES (5, '5-最低') ;
create table tbAttachment (fdIssueID int, fdFile varchar(128), fdPath varchar(64), fdName varchar(128), fdTime timestamp, fdUserID int) ;
create unique index nxAttachment on tbAttachment (fdIssueID,fdFile) ;
create table tbQuery (fdIssueID int, fdUserID int) ;
create index nxQuery_User on tbQuery (fdUserID) ;
create index nxQuery_Issue on tbQuery (fdIssueID) ;
create table tbType (id int auto_increment, fdName varchar(32), primary key(id)) ;
insert into tbType (fdName) VALUES ('功能无效') ;
insert into tbType (fdName) VALUES ('数据错误') ;
insert into tbType (fdName) VALUES ('系统故障') ;
insert into tbType (fdName) VALUES ('配置不当') ;
insert into tbType (fdName) VALUES ('新的需求') ;
insert into tbType (fdName) VALUES ('流程问题') ;
create table tbRole (id int, fdName varchar(32)) ;
insert into tbRole VALUES (0, '访客') ;
insert into tbRole VALUES (1, '操作录入') ;
insert into tbRole VALUES (2, '需求过滤') ;
insert into tbRole VALUES (4, '需求审核') ;
insert into tbRole VALUES (8, '项目经理') ;
insert into tbRole VALUES (16, '技术负责') ;
insert into tbRole VALUES (32, '开发实施') ;
insert into tbRole VALUES (64, '测试负责') ;
insert into tbRole VALUES (128, '验收确认') ;
create table tbGrant (fdTypeID int not null, fdStatusID int not null, fdRoleID int not null, fdField varchar(32), fdValue int) ;
create index nxGrant_TypeID on tbGrant (fdTypeID) ;
create index nxGrant_StatusID on tbGrant (fdStatusID) ;
create index nxGrant_RoleID on tbGrant (fdRoleID) ;
create index nxGrant_Field on tbGrant (fdField) ;
create table tbSetting (fdKey varchar(32) not null, fdValue varchar(128), primary key(fdKey)) ;
insert into tbSetting (fdKey,fdValue) VALUES ('manager', '1') ;
