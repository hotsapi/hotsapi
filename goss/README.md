# Goss
Goss is a standalone Go binary similar to servspec, which is used to validate server state: <https://github.com/aelsabbahy/goss>

We mount this directory in specific containers to determine health checks and dependent container status (e.g. artisan can't spin up until mysql has finished its init process)

