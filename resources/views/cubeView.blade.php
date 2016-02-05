<!DOCTYPE html>
<html>
    <head>
        <title>Configura tu cubo</title>
        <!-- Bootstrap Core CSS -->
        <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
    </head>
    <body>

        <section id="content">
            <div class="container">
                <div class="row"> 
                    {!! Form::open(array('url' => '/execute')) !!}
                    <div class="panel panel-default">
                        <div class="panel-heading">Cube calculator</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-md-12"> 
                                    <label for="input">Input: </label>
                                    {{ Form::textarea('input', $input, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12"> 
                                    <label for="output">Output: </label>
                                    {{ Form::textarea('output', $output, ['class' => 'form-control','disabled'=>'disabled']) }}
                                </div>
                            </div>
                            <div class="col-md-12" > 
                                <br>
                                {!! Form::submit('Execute',['class' => 'btn btn-warning btn-block' ]) !!}
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </section>



        <script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
    </body>
</html>