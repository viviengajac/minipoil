begin
	declare	v_niv2 int;
--	declare	v_nb_cles int;
--	declare	v_nom_col varchar(80);
	declare	v_nom_tab_fk varchar(80);
	declare	v_nom_tab_pk varchar(80);
	declare	v_nom_col_fk varchar(80);
	declare	v_nom_col_fk_pk varchar(80);
	declare	v_i	int;
--	declare	v_j	int;
	declare	v_id2 int;
--	declare	v_nb_id	int;
	declare	v_sql varchar(300);
	declare	v_nb_dep int;
--	declare	v_err int;
	declare	v_faire	int;
	declare	v_num_instr int;
	declare	v_msg_erreur varchar(500);
--	declare	v_val_erreur varchar(50);
--	declare	v_ajout int;
--	declare	v_min_ind int;
--	declare	v_max_ind int;
--	declare	v_ligne varchar(50);
	declare	v_nb int;
--	declare	v_app_ds_dep bit;
--	declare v_min_cle int;
--	declare v_max_cle int;
	declare v_retour_recurs char(2);
	declare v_curseur_ok int default 1;
	declare v_debog bit;
	declare v_nom_tab2 varchar(30);
	declare v_req_sql varchar(200);
	declare c_cles_b cursor for
		select nom_tab_fk,nom_col_fk,nom_col_fk_pk
		FROM info_sch_fk where nom_tab_pk=p_nom_tab
		order by 1,2,3;
		/*
		select RC.TABLE_NAME AS FK_TABLE_NAME,KCU.COLUMN_NAME,KCU.REFERENCED_COLUMN_NAME,KCU2.COLUMN_NAME
		FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS AS RC
		inner join INFORMATION_SCHEMA.TABLE_CONSTRAINTS FK on FK.CONSTRAINT_SCHEMA = RC.CONSTRAINT_SCHEMA and FK.CONSTRAINT_NAME = RC.CONSTRAINT_NAME and FK.CONSTRAINT_TYPE = 'FOREIGN KEY'
		inner join INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU on KCU.CONSTRAINT_NAME=RC.CONSTRAINT_NAME
		inner join INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU2 on KCU2.TABLE_NAME=RC.TABLE_NAME
		where rc.referenced_table_NAME=p_nom_tab and KCU2.CONSTRAINT_NAME='PRIMARY'
		order by 1,2,3;
		*/
	declare c_cles_h cursor for
		select nom_tab_pk,nom_col_fk,nom_col_fk_pk
		FROM info_sch_fk where nom_tab_fk=p_nom_tab
		order by 1,2,3;
		/*
		select RC.REFERENCED_TABLE_NAME AS FK_TABLE_NAME,KCU.COLUMN_NAME,KCU.REFERENCED_COLUMN_NAME,KCU2.COLUMN_NAME
		FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS AS RC
		inner join INFORMATION_SCHEMA.TABLE_CONSTRAINTS FK on FK.CONSTRAINT_SCHEMA = RC.CONSTRAINT_SCHEMA and FK.CONSTRAINT_NAME = RC.CONSTRAINT_NAME and FK.CONSTRAINT_TYPE = 'FOREIGN KEY'
		inner join INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU on KCU.CONSTRAINT_NAME=RC.CONSTRAINT_NAME
		inner join INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU2 on KCU2.TABLE_NAME=RC.TABLE_NAME
		where rc.table_NAME=p_nom_tab and KCU2.CONSTRAINT_NAME='PRIMARY'
		order by 1,2,3;
		*/
	declare c_id cursor for select id FROM temp_tab_id where niveau=p_niveau;
	declare continue handler for not found set v_curseur_ok=0;
	DECLARE exit handler for SQLEXCEPTION
	BEGIN
		GET DIAGNOSTICS CONDITION 1 @sqlstate = RETURNED_SQLSTATE, @errno = MYSQL_ERRNO, @text = MESSAGE_TEXT;
		set v_msg_erreur=concat('Erreur SQL :',@text);
		if (p_sens = 'B') then
			insert into temp_dep (niv,nom_tab,id,info) values (p_niveau+1,v_nom_tab_fk,null,v_msg_erreur);
		else
			insert into temp_dep (niv,nom_tab,id,info) values (p_niveau+1,p_nom_tab,null,v_msg_erreur);
		end if;
		set p_retour='KO';
--		drop table temp_id;
	END;
	set v_debog=1;
	if(v_debog=1) then
		call ap_debog(concat('debut de lec_dep_recurs(',p_sens,',',p_nom_tab,',',p_id,',',p_niveau,',',p_profondeur,')'));
	end if;
	/*
	if (p_niveau > 0) then
		select count(*) into v_nb from temp_dep where nom_tab=p_nom_tab and id=p_id;
		if (v_nb = 0) then
-- call ap_debog(concat('insertion d''une dependance(niveau=',p_niveau,', nom_tab=',p_nom_tab,', id=',p_id));
			insert into temp_dep (niv,nom_tab,id) values (p_niveau,p_nom_tab,p_id);
			set v_faire=1;
		else
			set v_faire=0;
		end if;
	else
		set v_faire=1;
	end if;
	*/
	select count(*) into v_nb from temp_dep where nom_tab=p_nom_tab and id=p_id;
	if (v_nb = 0) then
-- call ap_debog(concat('insertion d''une dependance(niveau=',p_niveau,', nom_tab=',p_nom_tab,', id=',p_id));
		insert into temp_dep (niv,nom_tab,id) values (p_niveau,p_nom_tab,p_id);
		set v_faire=1;
	else
		set v_faire=0;
	end if;
-- call ap_debog ('lec_dep_recurs 4');
	set v_niv2=p_niveau+1;
	if (v_faire>0 and v_niv2<32 and v_niv2<=p_profondeur) then-- Nombre d'imbrication dans T-sql doit être inférieur a 32
-- call ap_debog ('lec_dep_recurs 5');
		if (p_sens = 'B') then
			if (p_nom_tab = 'prsaaa') then
				set v_nb=0;
			else
				set v_faire=1;
			end if;
		else
			set v_faire=1;
		end if;
		if (v_faire>0) then
-- call ap_debog ('lec_dep_recurs 6');
			if (p_sens = 'B') then
				open c_cles_b;
			else
				open c_cles_h;
			end if;
-- call ap_debog ('lec_dep_recurs 7');
			set v_curseur_ok=1;
			while(v_curseur_ok=1) do
				if(p_sens = 'B') then
					fetch c_cles_b into v_nom_tab_fk,v_nom_col_fk,v_nom_col_fk_pk;
				else
					fetch c_cles_h into v_nom_tab_pk,v_nom_col_fk,v_nom_col_fk_pk;
				end if;
				if(v_curseur_ok=1) then
-- call ap_debog ('lec_dep_recurs 8');
--					delete from temp_tab_id where niveau=p_niveau;
--				create temporary table temp_tab_id (ind int not null auto_increment, primary key(ind),id int);
					if (p_sens = 'B') then
--						select @sql='declare lect_id cursor global for select '+@nom_pk+' from '+@nom_tab2+' where '+@nom_col2+'='+convert(varchar,@id)
-- call ap_debog ('6');
						set v_nom_tab2=v_nom_tab_fk;
						set v_req_sql=concat('insert into temp_tab_id(niveau,id) select ',p_niveau,',',v_nom_col_fk_pk,' from ',v_nom_tab2,' where ',v_nom_col_fk,'=',convert(p_id,char));
-- call ap_debog ('7');
-- call ap_debog(v_req_sql);
-- call ap_debog ('7 bis');
--						set @sql=concat('create or replace view temp_id as select ',v_nom_col_fk_pk,' as id from ',v_nom_tab2,' where ',v_nom_col_fk,'=',convert(p_id,char));
					else
						set v_req_sql=concat('insert into temp_tab_id(niveau,id) select ',p_niveau,',',v_nom_col_fk,' from ',p_nom_tab,' where ',v_nom_col_fk_pk,'=',convert(p_id,char));
--						select @sql='declare lect_id cursor global for select '+@nom_col+' from '+@nom_tab+' where id_'+@nom_tab+'='+convert(varchar,@id)
-- call ap_debog ('6');
						set v_nom_tab2=v_nom_tab_pk;
--						set @sql=concat('create or replace view temp_id as select ',v_nom_col_fk,' as id from ',p_nom_tab,' where ',v_nom_col_fk_pk,'=',convert(p_id,char));
-- call ap_debog ('7');
					end if;
					if(v_debog=1) then
						call ap_debog(concat('sql dyn=',v_req_sql));
					end if;
-- print 'sql='+isnull(@sql,'sql')
--					call sp_executesql (v_sql);
					set @sql=v_req_sql;
					prepare stmt from @sql;
					execute stmt;
					deallocate prepare stmt;
-- call ap_debog('apres exec sql dyn');
					open c_id;
					set v_curseur_ok=1;
					while(v_curseur_ok=1) do
						fetch c_id into v_id2;
						if(v_curseur_ok=1) then
							if (v_id2>0) then
								select count(*) into v_nb_dep from temp_dep;
-- call ap_debog(concat('v_nb_dep=',v_nb_dep));
								if (v_nb_dep < p_max_lignes) then
									set v_niv2=p_niveau+1;
-- call ap_debog(concat('v_niv2=',v_niv2));
									if (v_niv2<32 and v_niv2<=p_profondeur) then -- Nombre d'imbrication dans T-sql doit être inférieure à 32
										if(v_debog=1) then
											call ap_debog(concat('avant appel recursif(',p_sens,',',v_nom_tab2,',',v_id2,',',v_niv2,')'));
										end if;
										call lec_dep_recurs (p_sens,v_nom_tab2,v_id2,v_niv2,p_max_lignes,p_profondeur,v_retour_recurs);
										if(v_debog=1) then
											call ap_debog('apres appel recursif');
										end if;
									end if;
								end if;
							end if;
						end if;
					end while;
					if(v_debog=1) then
						call ap_debog('fin boucle sur les id');
					end if;
					delete from temp_tab_id where niveau=p_niveau;
					close c_id;
					set v_curseur_ok=1;
				end if;
				set v_i=v_i+1;
			end while;
			if(p_sens='B')then
				close c_cles_b;
			else
				close c_cles_h;
			end if;
			if(v_debog=1) then
				call ap_debog('fin boucle sur les cles');
			end if;
		end if;
	end if;
	set p_retour='OK';
-- print 'fin de lec_dep_recurs('+@sens+','+@nom_tab+','+convert(varchar,@id)+','+convert(varchar,@niveau)+','+convert(varchar,@profondeur)+')'
end