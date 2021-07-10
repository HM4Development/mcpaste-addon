{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    MCPaste configuration
@endsection

@section('content-header')
    <h1>MCPaste<small>Edit your MCPaste configuration here</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">MCPaste</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <form action="{{ route('admin.mcpaste') }}" method="post">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">General</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label for="token" class="control-label">Token</label>
                            <div>
                                <input type="text" name="token" value="{{ $config['token'] }}" class="form-control form-autocomplete-stop">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="postServerInfo" class="control-label">Display additional information at the top of the sent log</label>
                            <div>
                                @if($config['postServerInfo'])
                                    <input type="checkbox" name="postServerInfo" value="true" checked>
                                @else
                                    <input type="checkbox" name="postServerInfo" value="true">
                                @endif
                                <label for="postServerInfo">Post server info</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Button Design</h3>
                    </div>
                    <div class="box-body">
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                <tr>
                                    <td class="col-sm-3 strong">Button location</td>
                                    @if($config['design']['buttonLocation'] == 'component')
                                        <td class="col-sm-3 radio radio-primary text-center">
                                            <input type="radio" id="radio_component" name="buttonLocation" value="component" onchange="designUpdate()" checked>
                                            <label for="radio_component">Component</label>
                                        </td>
                                        <td class="col-sm-3 radio radio-primary text-center">
                                            <input type="radio" id="radio_command_line" name="buttonLocation" value="commandLine" onchange="designUpdate()">
                                            <label for="radio_command_line">Command line</label>
                                        </td>
                                    @else
                                        <td class="col-sm-3 radio radio-primary text-center">
                                            <input type="radio" id="radio_component" name="buttonLocation" value="component" onchange="designUpdate()">
                                            <label for="radio_component">Component</label>
                                        </td>
                                        <td class="col-sm-3 radio radio-primary text-center">
                                            <input type="radio" id="radio_command_line" name="buttonLocation" value="commandLine" onchange="designUpdate()" checked>
                                            <label for="radio_command_line">Command line</label>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </div>

                        <br>
                        <div id="componentDesign">
                            <div class="form-group col-md-3">
                                <label for="textColor" class="control-label">Text color</label>
                                <input type="color" name="textColor" value="{{ $config['design']['textColor'] ?? '#cad1d8' }}" class="form-control form-autocomplete-stop">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="textButtonColor" class="control-label">Text button color</label>
                                <input type="color" name="textButtonColor" value="{{ $config['design']['textButtonColor'] ?? '#3f4d5a' }}" class="form-control form-autocomplete-stop">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="textButtonHoverColor" class="control-label">Text button color on hover</label>
                                <input type="color" name="textButtonHoverColor" value="{{ $config['design']['textButtonHoverColor'] ?? '#515f6c' }}" class="form-control form-autocomplete-stop">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="boxColor" class="control-label">Box color</label>
                                <input type="color" name="boxColor" value="{{ $config['design']['boxColor'] ?? '#3f4d5a' }}" class="form-control form-autocomplete-stop">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="textSize" class="control-label">Text size</label>
                                <div>
                                    <select id="textSize" name="textSize" value="{{ $config['design']['textSize'] ?? 'text-xs' }}" class="form-control">
                                        <option value="text-xs">Extra small</option>
                                        <option value="text-sm">Small</option>
                                        <option value="text-base">Normal</option>
                                        <option value="text-lg">Large</option>
                                        <option value="text-xl">Extra large</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="buttonSize" class="control-label">Button size</label>
                                <div>
                                    <select id="buttonSize" name="buttonSize" value="{{ $config['design']['buttonSize'] ?? 'xsmall' }}" class="form-control">
                                        <option value="xsmall">Extra small</option>
                                        <option value="small">Small</option>
                                        <option value="large">Large</option>
                                        <option value="xlarge">Extra large</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="shadow" class="control-label">Enable shadow</label>
                                <div>
                                    @if($config['design']['shadow'] ?? true)
                                        <input type="checkbox" name="shadow" value="true" checked>
                                    @else
                                        <input type="checkbox" name="shadow" value="true">
                                    @endif
                                    <label for="shadow">Shadow</label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="textButtonText" class="control-label">Button text</label>
                                <input type="text" min="0" max="100" name="textButtonText" value="{{ $config['design']['textButtonText'] ?? "Send to McPaste.com" }}" class="form-control form-autocomplete-stop">
                            </div>
                        </div>

                        <div id="commandLineDesign">
                            <div class="form-group col-md-4">
                                <label for="icon" class="control-label">Icon</label>
                                <input type="text" name="icon" value="{{ $config['design']['icon'] ?? 'clipboard' }}" class="form-control form-autocomplete-stop">
                                <p class="text-muted small">Grab icons from <a href="https://fontawesome.com" target="_blank">https://fontawesome.com</a></p>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="iconColor" class="control-label">Icon color</label>
                                <input type="color" name="iconColor" value="{{ $config['design']['iconColor'] ?? '#e5e8eb' }}" class="form-control form-autocomplete-stop">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="iconHoverColor" class="control-label">Hover color</label>
                                <input type="color" name="iconHoverColor" value="{{ $config['design']['iconHoverColor'] ?? '#3f4d5a' }}" class="form-control form-autocomplete-stop">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Popup Design</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label for="toastTextColor" class="control-label">Text color</label>
                            <input type="color" name="toastTextColor" value="{{ $config['design']['toastTextColor'] ?? '#cad1d8' }}" class="form-control form-autocomplete-stop">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="toastBoxColor" class="control-label">Box color</label>
                            <input type="color" name="toastBoxColor" value="{{ $config['design']['toastBoxColor'] ?? '#cad1d8' }}" class="form-control form-autocomplete-stop">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="toastBorderColor" class="control-label">Box border color</label>
                            <input type="color" name="toastBorderColor" value="{{ $config['design']['toastBorderColor'] ?? '#cad1d8' }}" class="form-control form-autocomplete-stop">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="toastOpacity" class="control-label">Opacity</label>
                            <input type="number" min="0" max="100" name="toastOpacity" value="{{ $config['design']['toastOpacity'] ?? 75 }}" class="form-control form-autocomplete-stop">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="toastText" class="control-label">Text</label>
                            <input type="text" min="0" max="100" name="toastText" value="{{ $config['design']['toastText'] ?? 'Copied https://mcpaste.com/%key% to clipboard' }}" class="form-control form-autocomplete-stop">
                            <p class="text-muted small">Use %key% as a placeholder for the paste id.</p>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="toastErrorText" class="control-label">Error text</label>
                            <input type="text" min="0" max="100" name="toastErrorText" value="{{ $config['design']['toastErrorText'] ?? "Couldn't share log." }}" class="form-control form-autocomplete-stop">
                            <p class="text-muted small">Use %error% as a placeholder for the error message.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        {!! method_field('POST') !!}
                        <input type="submit" value="Update" class="btn btn-primary btn-sm">
                    </div>
                </div>
            </div>
        </form>
        <form action="{{ route('admin.mcpaste') }}" method="post">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        {!! method_field('DELETE') !!}
                        <input type="submit" value="Reset Design" class="btn btn-danger btn-sm">
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer-scripts')
    <script type="application/javascript">
        const radioComponent = document.getElementById('radio_component');
        const componentDesign = document.getElementById('componentDesign');
        const commandLineDesign = document.getElementById('commandLineDesign');

        designUpdate();

        function designUpdate() {
            // true if component, false if commandLine
            const buttonLocation = radioComponent.checked;

            if(buttonLocation) {
                componentDesign.style.display = '';
                commandLineDesign.style.display = 'none';
            } else {
                commandLineDesign.style.display = '';
                componentDesign.style.display = 'none';
            }
        }
    </script>

    @parent
    {!! Theme::js('vendor/lodash/lodash.js') !!}

    <!-- For auto-selecting values on <select> -->
    <script>
        $(document).ready(function() {
            $('select').each(function(index, id) {
                $('select#' + $(this).attr('id') + ' option[value=' + $(this).attr('value') + ']').attr('selected', true);
            });
        });
    </script>
@endsection
