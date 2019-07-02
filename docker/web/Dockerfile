# Pull base image
FROM amazonlinux:2018.03

# Install dependencies
COPY install.sh /root/install.sh
RUN bash /root/install.sh

# Create ec2-user
RUN useradd --home /home/ec2-user --create-home --shell /bin/bash --comment "EC2 Default User" --uid 500 --groups wheel ec2-user

# Forward nginx access and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log

# Install configuration files
COPY nginx.conf /etc/nginx/nginx.conf
COPY 00-default.conf /etc/nginx/conf.d/00-default.conf
COPY meucambio-core.conf /etc/nginx/conf.d/meucambio-core.conf

# Define default command.
CMD php-fpm -d variables_order="EGPCS" && exec nginx -g "daemon off;"

# Expose ports
EXPOSE 80
