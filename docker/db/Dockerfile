FROM mysql/mysql-server:8.0

ENV MYSQL_DATABASE=workshop \
  MYSQL_USER=admin \
  MYSQL_PASSWORD=admin \
  MYSQL_ROOT_PASSWORD=admin \
  TZ=Asia/Tokyo

COPY ./my.cnf /etc/my.cnf
RUN chmod 644 /etc/my.cnf