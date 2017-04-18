UPDATE acl_permiso set tipo_permiso = "REQUERIDO" WHERE blacklist = 1;
UPDATE acl_permiso set tipo_permiso = "NO_REQUERIDO" WHERE whitelist = 1;