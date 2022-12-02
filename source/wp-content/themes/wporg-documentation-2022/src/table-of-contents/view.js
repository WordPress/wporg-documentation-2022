const init = () => {
	const containers = document.querySelectorAll( '.wp-block-wporg-table-of-contents' );

	if ( containers ) {
		containers.forEach( ( element ) => {
			// 200 is an estimate for the fixed header height + top margin.
			const viewHeight = window.innerHeight - 200;
			// If the table of contents sidebar is shorter than the view area, apply the
			// class so that it's fixed and scrolls with the page content.
			if ( element.parentNode?.offsetHeight < viewHeight ) {
				element.parentNode.classList.add( 'is-fixed-sidebar' );
			}
		} );
	}
};

window.addEventListener( 'load', init );
