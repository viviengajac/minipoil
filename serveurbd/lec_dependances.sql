begin
	declare	v_fini int;
	declare	v_tab_id_tmp varchar(500);
	declare	v_pos int;
	declare	v_id int;
	declare	v_tmp varchar(500);
	declare	v_msmt bit;
	declare v_nb int;
	declare v_ret_recurs char(2);
SET @@GLOBAL.max_sp_recursion_depth = 7;
SET @@SESSION.max_sp_recursion_depth = 7;
-- print 'lec_dependances('+@sens+','+@nom_tab+','+@tab_id+','+convert(varchar,@max_lignes)+','+convert(varchar,@profondeur)+')'
	select locate(';',p_tab_id) into v_pos;
	if (v_pos = 0) then
		set p_tab_id=concat(p_tab_id,';');
	end if;
	select count(*) into v_nb from information_schema.innodb_temp_table_info where name='temp_dep'; -- where n_cols=8;
-- call ap_debog(concat('nb pour temp_dep=',v_nb));
	if(v_nb = 0) then
--		 call ap_debog('avant creer temp table');
		create temporary table temp_dep (id_dep int not null auto_increment,primary key(id_dep),niv int,nom_tab varchar(40),id int,info varchar(300));
	else
		delete from temp_dep;
	end if;
	/*
	select count(*) into v_nb from information_schema.innodb_temp_table_info where n_cols=6; -- where name='temp_dep';
-- call ap_debog(concat('nb pour temp_tab_cle=',v_nb));
	if(v_nb = 0) then
		create temporary table temp_tab_cle (ind int not null auto_increment,primary key(ind),niveau int,nom_col varchar(80),nom_tab2 varchar(80),nom_col2 varchar(80),nom_pk varchar(80));
	end if;
	*/
	select count(*) into v_nb from information_schema.innodb_temp_table_info where name='temp_tab_id'; -- where n_cols=6;
-- call ap_debog(concat('nb pour temp_tab_id=',v_nb));
	if(v_nb = 0) then
		create temporary table temp_tab_id (ind int not null auto_increment,primary key(ind),niveau int,id int);
	else
		delete from temp_tab_id;
	end if;
-- call ap_debog ('lec_dep_recurs 2');
-- call ap_debog('apres creer temp table');
	set v_fini=0;
	set v_tab_id_tmp=p_tab_id;
	while (v_fini = 0) do
-- call ap_debog('avant locate');
		select locate(';',v_tab_id_tmp) into v_pos;
-- call ap_debog(concat('apres locate, v_pos=',v_pos));
-- call ap_debog(concat('v_tab_id=',v_tab_id_tmp,', v_pos=(',v_pos,')'));
-- print 'pos='+convert(varchar,@pos)
		if (v_pos <= 0) then
			set v_fini=1;
		else
			if (v_pos > 1) then
-- call ap_debog(concat('avant v_id=substring..:tab_id_tmp=(',v_tab_id_tmp,'), v_pos=',v_pos));
				set v_id=substring(v_tab_id_tmp,1,v_pos-1);
-- call ap_debog('apres v_id=substring...');
--				set v_id=convert(v_tmp as int(10));
-- print 'id='+convert(varchar,@id)
-- print 'profondeur='+convert(varchar,@profondeur)
-- call ap_debog('avant appel lec_dep_recurs');
				call lec_dep_recurs (p_sens,p_nom_tab,v_id,0,p_max_lignes,p_profondeur,v_ret_recurs);
-- call ap_debog('apres appel lec_dep_recurs');
			end if;
-- call ap_debog('avant v_tmp');
			select substring(v_tab_id_tmp,v_pos+1,999) into v_tmp;
-- call ap_debog('apres v_tmp');
			set v_tab_id_tmp=v_tmp;
-- call ap_debog(concat('apres v_tab_id, v_tab_id_tmp=',v_tab_id_tmp));
		end if;
	end while;
-- call ap_debog('avant drop tab_id');
--	drop table temp_tab_id;
-- call ap_debog('avant drop tab_cle');
--	drop table temp_tab_cle;
-- call ap_debog('apres drop tab_cle');
--	select @msmt=msmt from prj
--	select id_dep,niv,nom_tab,isnull(sql,dbo.fct_rep_dependances(nom_tab,id,@msmt)) as rep,id from #dep order by id_dep
--	select id_dep,niv,nom_tab,dbo.fct_rep_dependances(nom_tab,id,@msmt) as rep,id from #dep order by id_dep
--	select id_dep,niv,nom_tab,fct_rep(nom_tab,id) as rep,id,info from temp_dep order by id_dep;
-- insert into debog(msg) select concat(id_dep,',',niv,',',nom_tab,',',id,',',info) from temp_dep;
-- call ap_debog('apres insert into temp_dep');
	select niv,case isnull(lib_tab) when 1 then t.nom_tab else lib_tab end as nom_tab,id,fct_rep(t.nom_tab,id) as rep,info
	from temp_dep t left outer join AZtab on t.nom_tab=AZtab.nom_tab order by id_dep;
--	drop table temp_tab_cle;
	drop table temp_tab_id;
	drop table temp_dep;
end