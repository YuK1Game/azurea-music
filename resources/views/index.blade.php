<html>
    <head>
        <title>AZUREA MusicXML → MML変換ツール</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
        <style type="text/css">
            h2 {
                padding: 0.5em;
                color: #494949;
                background: #fffaf4;
                border-left: solid 5px #ffaf58;
            }
        </style>
    
    </head>
    <body>
        <div class="mt-3">
            <form id="file_upload_form" method="post" enctype="multipart/form-data" action="{{ url('/') }}">
                <div id="file_drag_drop_area" class="text-center p-3 rounded col-md-10 mx-auto" style="border:3px #000000 dashed;">
                    ここにファイルをドラッグ&ドロップ<br/>
                    <span>または</span><br/>
                    <input id="file_input" type="file" name="file[]" multiple accept=".mxl"/>
                </div>

                @if (isset($errors))
                    @foreach ($errors as $error)
                        <div class="alert alert-danger" role="alert">
                            {{ $error }}
                        </div>
                    @endforeach
                @endif

                <div class="d-flex justify-content-center mt-2">
                    <input type="submit" value="送信" class="btn btn-primary pl-3 pr-3" />
                </div>

                {{ csrf_field() }}
            </form>
        </div>

        @if (isset($parts))
            <div class="container">
                @foreach ($parts as $part)
                    <div class="row mb-5 ">
                            <h2>{{ $part->get('part_name') }} ({{ $part->get('id') }})</h2>
                            
                            @foreach ($part->get('tracks') as $trackIndex => $track)
                                <div class="col mb-4">
                                    <div >
                                        <h4>Track : {{ $trackIndex }}</h4>
                                    </div>
                                    <div id="part-{{ $part->get('id') }}-track-{{ $trackIndex }}" class="mb-2" style="white-space : pre-line; max-height : 200px; overflow-y : scroll">
                                        <ul class="list-group">
                                            @foreach ($track->get('measures') as $measureIndex => $notes)
                                                <li class="list-group-item">[{{ $measureIndex }}] {{ $notes->flatten()->join('') }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-primary" data-copy-id="part-{{ $part->get('id') }}-track-{{ $trackIndex }}">コピー</button>
                                </div>
                            @endforeach
                    </div>
                @endforeach
            </div>
        @endif

        <script src="//code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="></script>
        <script>
            $(function(){
                // ドラッグしたままエリアに乗った＆外れたとき
                $(document).on('dragover', '#file_drag_drop_area, #file_drag_drop_area_stl', function (event) {
                    event.preventDefault();
                    $(this).css("background-color", "#999999");
                });
                $(document).on('dragleave', '#file_drag_drop_area, #file_drag_drop_area_stl', function (event) {
                    event.preventDefault();
                    $(this).css("background-color", "transparent");
                });

                // ドラッグした時
                $(document).on('drop', '#file_drag_drop_area', function (event) {
                    let org_e = event;
                    event.originalEvent && (org_e = event.originalEvent);
                    org_e.preventDefault();
                    file_input.files = org_e.dataTransfer.files;
                    $(this).css("background-color", "transparent");
                });        
                
                // クリップボードにコピー
                $('button[data-copy-id]').click(function() {
                    const copyId = $(this).attr('data-copy-id');
                    const list = $('#' + copyId + ' > ul > li');
                    const text = [ ...list ].map(row => {
                        return $(row).text();
                    }).join("\n");
                    navigator.clipboard.writeText(text);

                    alert('コピー完了！');
                });
            });
        </script>
    </body>
</html>