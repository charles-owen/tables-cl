/** @file
 * Testing support class (Javascript)
 * @author Charles B. Owen
 * @version 2.01 12-07-2014 Declared a version number
 * Much more sophisticated and cleaner installation and operation.
 */

/** Testing support class 
 *
 * This class maintains a list of all available tests and
 * can create the HTML for the test links. It also maintains
 * a list of pending tests so tests are done sequentially, since
 * overlapping database tests may interfere.
 *
 * Requires an empty div for the test results presentation.
 * @param id Empty div in which to put the tests
 * @param resultsid div in which to put the test results
 */
var Tests = function (id, resultsid) {
    var that = this;	///< Trick to access members from private function
	this.tests = [];	///< All tests available to run
	this.to_run = [];	///< Tests that are pending to be run
	var testsdiv = id + "_tests";		///< Where to put the tests
	var testsdivid = '#' + testsdiv;	///< testsdiv with leading #
	var resultsdivid = '#' + resultsid; ///< resultsdiv with leading #
	
	/** Run a single test 
	 * @param test Name of the class to test */
	function run_a_test(test) {
		$.ajax({
			url : "testrunner.php",
			type: "POST",
			data : "test="+test+'.php',
			success: function(data, textStatus, jqXHR)
			{
				//$('#results').append(data);	
				var res = data.split('\n');
				var html = '';
				var i;
				if($('#verbose').is(':checked')) {
                    html += verbose(res);
				} else {

					if(data.search("FAILURE") >= 0) {
						html += verbose(res);
					} else if(data.search("incomplete") >= 0) {
                        html += verbose(res);
					} else if(data.search("OK") >= 0) {
						// Tests did not fail
						var i;
						for(i=4; i<res.length;  i++) {
							if(res[i].search("OK") >= 0) {
								html += '<p class="success">' + test + ": " + res[i] + '</p>';
							}
						}
					} else {
                        html += verbose(res);
					}
				}

				html = $('<textarea/>').html(html).text();

				$(resultsdivid).append(html);
				test_done();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$(resultsdivid).append("error: " + jqXHR + ' ' + textStatus + ' ' + errorThrown);	
				test_done();
			}
		});
	}

    /** Display output in a verbose manner including HTML entities
     *
     * This will convert the output to HTML entities and put it into a
     * pre tag so it is displayed exactly as provided.
     * @param res The server response split into lines
     * @returns HTML
     */
    function verbose(res) {
        // Verbose output - everything is echoed
        html = '<pre>';
        text = '';
        for(i=0; i<res.length;  i++) {
            text += res[i] + "\n";
        }
        html += html_encode(text);
        html += '</pre>';
        return html;
    }

    /** Encode html so entities are converted to visible values
     * @param s String to encode
     * @returns HTML result
     */
    function html_encode(s) {
        return $("<div/>").text(s).html();
    }
	
	/// Private function called when test is done
	function test_done() {
		if(that.to_run.length > 0) {
			that.start();
		} else {
			$(resultsdivid).append('<p class="complete">Tests Complete</p>');	
		}
	}
	
	/// Start running tests 
	this.start = function() {
		if(this.to_run.length > 0) {
			run_a_test(this.to_run.shift());
		}
	}	

	/** Public function to run a single test on demand 
	 * @param test The name of the clss to test */
	this.run_test = function(test) {
		this.to_run = [];
		this.clear();
		var p = $('<p></p>').appendTo(resultsdivid);
		//var li = $('<li></li>').appendTo(ul);
		$('<a>', {
			text: "Execute " + test + " Again",
            href: "#",
			click: function(event) {
                event.preventDefault();
				that.run_test(test);
			}
		}).appendTo(p);
		
		run_a_test(test);
	}
	
	/** Clear the test results display area */
	this.clear = function() {
		$("#resultsdiv").html('');
	}
	
	/** Add a test to the collection of tests for this object
	 * @param test Tests to add as an array */
	this.add = function(test) {
		this.tests.push(test);
	}
	
	/** Display the links for all of the tests
	 *
	 * This creates a <ul> tag in testsdiv and adds
	 * the tests to it. 
	 */
	this.display = function() {
		var ul = $('<ul class="tests" />').appendTo(testsdivid);
		html = '';
		for(i in this.tests) {
            var test = this.tests[i];
			var t = test;
			var tt = t + "Test";
			// The <li> tag for the item
			var li = $('<li />').appendTo(ul);
			var a = $('<a>', {
				href: resultsdivid,
				text: t,
				// This executes the outer function creating a context
				// that is passed the value tt, then the inner function
				// is returned with a reference to the tt in that context
				click: function(tt) {
					return function() {
						that.run_test('phpunit/' + tt);
					}
				}(tt)
			}).appendTo(li);
		}
	}
	
	/** Do all of the tests */
	this.do_all = function() {
		this.clear();
		this.to_run = [];
		for(test in this.tests) {
			this.to_run.push('phpunit/' + this.tests[test] + 'Test');
		}
		this.start();
	}
	
	// Add a div for the tests and the "all" tests
	$('<div />', {	// First div
		id: testsdiv
	}).appendTo('#' + id);

	var ul = $('<ul></ul>').appendTo('#' + id);
	var li = $('<li></li>').appendTo(ul);
	$('<a>', {
		text: 'All/one at a time',
		href: resultsdivid,
		click: function() {
			that.do_all();
		}
	}).appendTo(li);
	
	var li = $('<li></li>').appendTo(ul);
	$('<a>', {
		text: 'All/together',
		href: resultsdivid,
		click: function() {
			that.run_test('all');
		}
	}).appendTo(li);

	$('.clearresults').click( function(event) {
        event.preventDefault();
		that.clear();
	});
	
	this.clear();
}