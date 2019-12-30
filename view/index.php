<?php
    /**
     *  Elements style bootstrap: https://bootswatch.com/cosmo/
     * 
     */
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Home</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="/view/style/bootstrap/bootstrap.css" />
        
        <?php RouterClass::Style("Bootstrap"); ?>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <a class="navbar-brand" href="#">CarCentinel - Wiki</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Categoria 1</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Categoria 2</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Categoria 3</a>
                    </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="text" placeholder="Search">
                    <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
                </form>
            </div>
        </nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Wiki</a></li>
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Library</a></li>
            <li class="breadcrumb-item active">Data</li>
        </ol>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-2">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active">
                            Cras justo odio
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            Dapibus ac facilisis in
                        </a>
                        <a href="#" class="list-group-item list-group-item-action disabled">
                            Morbi leo risus
                        </a>
                    </div>
                </div>
                <div class="col-sm-10">
                    <h2>Example body text</h2>
                    <p>Nullam quis risus eget <a href="#">urna mollis ornare</a> vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula.</p>
                    <p><small>This line of text is meant to be treated as fine print.</small></p>
                    <p>The following is <strong>rendered as bold text</strong>.</p>
                    <p>The following is <em>rendered as italicized text</em>.</p>
                    <p>An abbreviation of the word attribute is <abbr title="attribute">attr</abbr>.</p>
                </div>
                <div class="col-sm-12">
                <table class="table table-hover">
                    <thead>
                        <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Column heading</th>
                        <th scope="col">Column heading</th>
                        <th scope="col">Column heading</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-active">
                        <th scope="row">Active</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                        <tr>
                        <th scope="row">Default</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                        <tr class="table-primary">
                        <th scope="row">Primary</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                        <tr class="table-secondary">
                        <th scope="row">Secondary</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                        <tr class="table-success">
                        <th scope="row">Success</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                        <tr class="table-danger">
                        <th scope="row">Danger</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                        <tr class="table-warning">
                        <th scope="row">Warning</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                        <tr class="table-info">
                        <th scope="row">Info</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                        <tr class="table-light">
                        <th scope="row">Light</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                        <tr class="table-dark">
                        <th scope="row">Dark</th>
                        <td>Column content</td>
                        <td>Column content</td>
                        <td>Column content</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>

        </div>

        <?php RouterClass::JS("Bootstrap"); ?>
    </body>
</html>


