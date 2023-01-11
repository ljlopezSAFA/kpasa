create table mensaje(
                        id  int(10) not null auto_increment,
                        descripcion varchar(150) not null,
                        fecha datetime not null,
                        primary key(id)
);


create table usuario(
                        id  int(10) not null auto_increment,
                        username varchar(150) not null,
                        password varchar(150) not null,
                        primary key(id)
);

create table perfil(
                       id  int(10) not null auto_increment,
                       nombre varchar(150) not null,
                       apellidos varchar(150) not null,
                       fecha_nacimiento date not null,
                       sexo int(1) not null,
                       id_usuario int(10),
                       primary key(id),
                       constraint perfil_usuario_fk foreign key (id_usuario) references usuario(id)
);


insert into mensaje values(1, "Hola muy buenas", "2022-12-20 15:00:00");
insert into mensaje values(2, "¿Cómo estás?", "2022-12-20 15:01:00");
insert into mensaje values(3, "Chungo", "2022-12-20 15:02:00");