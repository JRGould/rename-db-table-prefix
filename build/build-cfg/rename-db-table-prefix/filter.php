<?php
chdir( $src_dir );
system( 'npm install' );
system( 'npm run dist' );
chdir( $src_dir );
