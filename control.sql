INSERT INTO tbTable 
  	(fdName,fdDescription) 
	VALUES 
	 	( 'tbControlTable', '数据表' ), 
		('tbControlField', '数据字段') ;
INSERT INTO tbField 
		(fdTableID,fdName,fdDescription) 
	VALUES 
		( 1, 'fdName', '表名' ), 
		( 1, 'fdDescription', '名称' ) ;
INSERT INTO tbRelate
	  ( fdMaster, fdSlave, fdMasterField, fdSlaveField )
	VALUES
		( "tbTable", "tbField", "id", "fdTableID" ) ;
