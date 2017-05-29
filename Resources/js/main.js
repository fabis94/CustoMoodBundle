// GENERAL DESIGN FUNCTIONS AND EVENT HANDLERS
var CM = {
    /**
     * CSS Breakpoints
     */
    BREAKPOINTS: {
        xsm: 400,
        sm: 768,
        md: 1024,
        lg: 1200
    },
    /**
     * Replicates CSS breakpoint functionality
     * @param type
     * @param maxWidth
     */
    breakpoint: function(type, maxWidth) {
        var w = CM.getWidth();
        type = type !== 'undefined' ? type : CM.BREAKPOINTS.sm;
        maxWidth = maxWidth !== 'undefined' ? maxWidth : false;

        return (maxWidth && w <= type) || (!maxWidth && w < type);
    },
    /**
     * Get viewport width
     * @returns {number}
     */
    getWidth: function() {
        return Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    },
    /**
     * Get viewport height
     * @returns {number}
     */
    getHeight: function() {
        return Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    },
    init: function() {
        // Init other scripts
        chart.init();

        // GLOBAL EVENT HANDLERS
        var resizeEventHandler = function(e) {
            var h = CM.getHeight();
            var w = CM.getWidth();

        };
        $(window).on('resize', resizeEventHandler);
        resizeEventHandler();
    }
};

$(document).ready(function() {
    CM.init();
});