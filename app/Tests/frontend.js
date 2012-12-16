var stepNb = 1;

if (!casper.cli.has('url')) {
    casper.echo('You need to specify --url');
    casper.exit();
}

casper.start(gasper.cli.get('url'));
casper.viewport(1024, 768);

casper.then(function() {
    this.echo('Testing homepage')
    this.test.assertTestExists('Welcome', '"Welcome" is displayed');
    this.capture('screen.png');
    this.clickLabel("Run The Demo", 'a');
});

casper.then(function() {
    this.echo('Test demo area');
    this.test.assertTextExists('Available demos', 'We arrive on demo homepage');
    this.capture('screen2.png');
});

casper.run(function() {
    this.test.done(1);
});

casper.on('step.complete', function (stepResult) {
    this.capture('screen' + stepNb + '.png');
    stepNb++;
})