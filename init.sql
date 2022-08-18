create table tbStatus (id int not null, fdName varchar(32), primary key (id) ) ;
insert into tbStatus VALUES (1, 'δ�ύ') ;
insert into tbStatus VALUES (2, '���ύδ���') ;
insert into tbStatus VALUES (4, '�����δ����') ;
insert into tbStatus VALUES (8, '�ѷ���δ����') ;
insert into tbStatus VALUES (16, '�Ѱ���δ���') ;
insert into tbStatus VALUES (32, '�����δ����') ;
insert into tbStatus VALUES (64, '�Ѳ���δȷ��') ;
insert into tbStatus VALUES (128, '��ȷ��δ����') ;
insert into tbStatus VALUES (256, '�ѷ������') ;
insert into tbStatus VALUES (512, '�ѹر�') ;
create table tbModule (id int auto_increment, fdName varchar(32), primary key (id) ) ;
insert into tbModule values ( 0, '��ȷ��' ) ;
insert into tbModule values ( 1, '�Ʒ�' ) ;
insert into tbModule values ( 2, '����' ) ;
insert into tbModule values ( 3, '����');
insert into tbModule values ( 4, 'Ӫҵ') ;
insert into tbModule values ( 5, 'Ӫ��') ;
insert into tbModule values ( 6, '��ҵ��') ;
insert into tbModule values ( 7, '��Դ') ;
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
insert into tbPriority (id, fdName) VALUES (1, '1-���') ;
insert into tbPriority (id, fdName) VALUES (2, '2-��') ;
insert into tbPriority (id, fdName) VALUES (3, '3-��') ;
insert into tbPriority (id, fdName) VALUES (4, '4-��') ;
insert into tbPriority (id, fdName) VALUES (5, '5-���') ;
create table tbAttachment (fdIssueID int, fdFile varchar(128), fdPath varchar(64), fdName varchar(128), fdTime timestamp, fdUserID int) ;
create unique index nxAttachment on tbAttachment (fdIssueID,fdFile) ;
create table tbQuery (fdIssueID int, fdUserID int) ;
create index nxQuery_User on tbQuery (fdUserID) ;
create index nxQuery_Issue on tbQuery (fdIssueID) ;
create table tbType (id int auto_increment, fdName varchar(32), primary key(id)) ;
insert into tbType (fdName) VALUES ('������Ч') ;
insert into tbType (fdName) VALUES ('���ݴ���') ;
insert into tbType (fdName) VALUES ('ϵͳ����') ;
insert into tbType (fdName) VALUES ('���ò���') ;
insert into tbType (fdName) VALUES ('�µ�����') ;
insert into tbType (fdName) VALUES ('��������') ;
create table tbRole (id int, fdName varchar(32)) ;
insert into tbRole VALUES (0, '�ÿ�') ;
insert into tbRole VALUES (1, '����¼��') ;
insert into tbRole VALUES (2, '�������') ;
insert into tbRole VALUES (4, '�������') ;
insert into tbRole VALUES (8, '��Ŀ����') ;
insert into tbRole VALUES (16, '��������') ;
insert into tbRole VALUES (32, '����ʵʩ') ;
insert into tbRole VALUES (64, '���Ը���') ;
insert into tbRole VALUES (128, '����ȷ��') ;
create table tbGrant (fdTypeID int not null, fdStatusID int not null, fdRoleID int not null, fdField varchar(32), fdValue int) ;
create index nxGrant_TypeID on tbGrant (fdTypeID) ;
create index nxGrant_StatusID on tbGrant (fdStatusID) ;
create index nxGrant_RoleID on tbGrant (fdRoleID) ;
create index nxGrant_Field on tbGrant (fdField) ;
create table tbSetting (fdKey varchar(32) not null, fdValue varchar(128), primary key(fdKey)) ;
insert into tbSetting (fdKey,fdValue) VALUES ('manager', '1') ;
