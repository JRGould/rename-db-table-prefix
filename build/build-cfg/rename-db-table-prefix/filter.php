<?php
chdir( $src_dir );
echo 'CWD: ' . getcwd();
system( 'yarn install' );
system( 'yarn run dist' );
chdir( $src_dir );
