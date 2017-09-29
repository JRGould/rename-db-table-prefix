<?php
if ( ! getenv('RDTPPUBLISH') ) {
	return;
}

echo 'Publish to WP.org? (Y/n) ';
if ( 'Y' == strtoupper( trim( fgets( STDIN ) ) ) ) {
	system( 'rm -fR svn' ); // Cleanup before checkout to prevent errors
	system( 'svn co -q https://plugins.svn.wordpress.org/rename-db-table-prefix/ svn' );
	system( 'rm -R svn/trunk' );
	system( 'mkdir svn/trunk' );
	system( 'mkdir svn/tags/$version' );
	system( 'rm -R svn/assets' );
	system( 'mkdir svn/assets' );
	system( "rsync -r $plugin_slug/* svn/trunk/" );
	system( "rsync -r $plugin_slug/* svn/tags/$version" );
	system( "rsync -r ../../screenshots/* svn/assets/" );
	system( 'svn stat svn/ | grep \'^\?\' | awk \'{print $2}\' | xargs -I x svn add x@' );
	system( 'svn stat svn/ | grep \'^\!\' | awk \'{print $2}\' | xargs -I x svn rm --force x@' );
	system( 'svn stat svn/' );

	echo 'Commit to WP.org? (Y/n)? ';
	if ( 'Y' == strtoupper( trim( fgets( STDIN ) ) ) ) {
		system( "svn ci --username JRGould svn/ -m 'Deploy version $version'" );
	}

	system( 'rm -fR svn' ); // All done
}

