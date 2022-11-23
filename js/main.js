function save_row(row_id) {
    let $input_elements = $("input[type='text'][data-id='"+row_id+"'][data-field^='price']")

    let field_values = {}
    $input_elements.each(function (index, current) {
        field_values[$(current).attr('data-field')] = $(current).val().length > 0 ? parseFloat($(current).val().replace(",", ".")) : ""
    })

    $.ajax({
        url: "ajax/get_current_values.php",
        type: "GET",
        data: {
            data_id: row_id
        },
        statusCode: {
            404: function () {
                console.log("not found")
            }
        },
        success: function (data) {
            if(Object.keys(data).includes("success")) {
                if(data["success"]) {
                    is_not_same = {}
                    $.each(field_values, function (current_field_name) {
                        if(Object.keys(data["data"]).includes(current_field_name)) {
                            // if(data["data"][current_field_name] != field_values[current_field_name]) {
                                is_not_same[current_field_name] = {
                                    "old": data["data"][current_field_name],
                                    "new": field_values[current_field_name]
                                }
                            //}
                        }
                    })

                    if(Object.keys(is_not_same).length > 0) {

                        $.ajax({
                            url: "ajax/update_current_values.php",
                            type: "POST",
                            data: {
                                update: true,
                                new_values: is_not_same,
                                current_row: row_id
                            },
                            statusCode: {
                                404: function () {
                                    console.log("not found")
                                }
                            },
                            success: function (data) {
                                console.log(data["vals"])
                                let parent_ = $("input[type='button'][data-ident='save_btn'][data-id='"+row_id+"']").parent().parent()
                                console.log(parent_)
                                $(parent_).css({
                                    background: "#75da92",
                                    color: "#333"
                                })
                                window.setTimeout(function () {
                                    $(parent_).animate({
                                        background: "#d3ffe1",
                                        color: "#000"
                                    }, 750, '', function () {
                                        $(this).css({
                                            background: "#d3ffe1",
                                            color: "#000"
                                        })
                                    })
                                }, 750)


                            }
                        })
                    }

                }
            }
        }
    })
}

function removeItemOnce(arr, index) {
    return  arr.filter(function(current_item, current_index) {
        return current_index !== index
    });
}

function resetChanges() {
    $.ajax({
        url: "ajax/reset_changes.php",
        dataType: 'json',
        method: "GET",
        success: function (data) {
            console.log(data)
        }
    })
    load_preview_table()
}

function update_database() {
    $.ajax({
        url: "ajax/update_database.php",
        dataType: 'json',
        method: "POST",
        data: {
            save: true
        },
        success: function (data) {
            if($.inArray("success", Object.keys(data)) != "-1") {
                if(data["success"] == true) {
                    resetChanges()
                    window.location.reload()
                } else {
                    alert("errors detected");
                    console.log(data["updated"]);
                    console.log(data["errors"]);
                }
            }
        }
    })

}

function fadeInElement(close=false) {
    if(close == true) {
        $("#preview_table").animate({
            right: -755
        },250, '').addClass("closed");
        myBlurFunction(0)

        $('html, body').css({
            overflow: 'auto',
            height: 'auto'
        });

        return;
    } else {
        if(!$("#preview_table").hasClass("closed")) {
            $("#preview_table").animate({
                right: -755
            },250, '').addClass("closed");
            myBlurFunction(0)
        } else {
            $('html, body').css({
                overflow: 'hidden',
                height: '100%'
            });

            $("#preview_table").animate({
                right: 0
            },150, '').removeClass("closed").removeAttr('class');
            myBlurFunction(1)
            $("#overlay").on('click', function (ev) {
                console.log(ev)
                ev.preventDefault();
                fadeInElement(true)
                myBlurFunction(0)
            })
        }
    }




}

var myBlurFunction = function(state) {
    /* state can be 1 or 0 */
    var containerElement = document.getElementById('content');
    var overlayEle = document.getElementById('overlay');

    if (state) {
        overlayEle.style.display = 'block';
        containerElement.setAttribute('class', 'blur');
    } else {
        overlayEle.style.display = 'none';
        containerElement.setAttribute('class', null);
    }
};

function validate_scroll_pos(pos_){
    let scroll = window.scrollY;
    if(scroll > pos_) {
        $("section#header").css({
            position: "fixed",
            width: "100%",
            top: '0.1em',
            right: '1.1rem',
            /* margin-bottom: 2em; */
            border: "1px solid #333",
            fontSize: '2.8rem',
            zIndex: 150,
            minHeight: '5vh',
            marginTop: '',
            display: 'none'
        })

        $("section#footer").css({
            fontSize: '.6rem'
        })
        $("#login_link").css({
            float: 'left',
            position: 'fixed',
            top: '0.25em',
            right: '1.1rem',
            fontSize: '1.5rem',
            display: 'block',
            border: '1px solid #24242459',
            padding: '0.2em',
            background: '#ECECEC',
            zIndex: 150,
            paddingLeft: '0.4em',
            paddingRight: '0.4em',
            paddingTop: '0.2em',
            paddingBottom: '0.2em'
        })
        $(".preview_container_btn").css({
            "position": "fixed",
        })
    } else {
        $(".preview_container_btn").css({
            "position": "",
        })
        $("section#header").css({
            position: '',
            top: '',
            left: '',
            border: '',
            fontSize: '1.2rem',
            marginTop: '13vh !important',
            display: ''

        })

        $("section#footer").css({
            fontSize: '.8rem'
        })
        $("#login_link").css({
            float: '',
            position: '',
            top: '',
            right: '',
            fontSize: '',
            display: '',
            border: '',
            padding: '',
            background: '#ECECEC',
            paddingLeft: '0.4em',
            paddingRight: '0.4em',
            paddingTop: '0.2em',
            paddingBottom: '0.2em',
        })

    }
}

let changes = [];

function load_preview_table() {
    $(document).ready(function () {

    console.log("ready")
    $.ajax({
        url: "ajax/preview.php",
        dataType: 'json',
        method: "GET",
        before: function () {
            myBlurFunction(1)
        },
        after: function () {
            myBlurFunction(0)
        },
        success: function (data) {
            let ret = false
            console.log("success load prev")
            if ($.inArray("success", Object.keys(data)) != "-1") {
                let ret = data["success"]
            }

            let wrapper_div = document.createElement('div')
            wrapper_div.id = 'overlay'

            var new_div = document.createElement('div')

            new_div.id = "preview_table"
            new_div.className = "closed"

            let html_content = ""

            if($.inArray("html", Object.keys(data)) != "-1") {
                html_content += data["html"]
            } else {
                if(!ret) {
                    console.log(data)
                    html_content += "<div style='width: 100%;'><h1>Error! (or no changes)</h1><h2>" + data["message"] + "</h2></div>"
                }
            }

            html_content += $(new_div).html()+"\n<div style='width: 100vw;'><h4>Click anywhere to return</h4></div>"

            $(new_div).html(html_content)
            $(wrapper_div).append(new_div)

            if($(document).find($("#overlay")).length == 0) {
                $(document).find("body").append(wrapper_div)
            } else {
                console.log(document.getElementById('overlay'))
                $(document).find($("#overlay")).remove()
                $(document).find("body").append(wrapper_div)
            }

        }
    })

    })
}

let isMobile = false; //initiate as false
$(document).ready(function () {

    $("#login_link").css({
        float: 'left',
        position: 'fixed',
        top: '0.25em',
        right: '1.1rem',
        fontSize: '1.5rem',
        display: 'block',
        border: '1px solid #24242459',
        padding: '0.2em',
        background: '#ECECEC',
        zIndex: 1,
        paddingLeft: '0.4em',
        paddingRight: '0.4em',
        paddingTop: '0.2em',
        paddingBottom: '0.2em'
    })


    // device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
        isMobile = true;
    }
    if(isMobile) {
    }

    /*
    validate_scroll_pos(200)

    window.addEventListener('scroll', function () {
        validate_scroll_pos(200)
    });
    */

    // load_preview_table()




    $("#create_backup_to_file_btn").on('click', function (ev){
        ev.preventDefault();
        $.ajax({
            url: "ajax/create_backup.php",
            dataType: 'json',
            method: "POST",
            data: {
                backup: true
            }, success: function (data) {
                if($.inArray("success", Object.keys(data)) != "-1") {
                    if(data["success"]) {
                        window.location.reload()
                    } else {
                        alert(" could not create backup!");
                    }
                }
            }
        })
    })

    $("#load_backup_from_file_btn").on('click', function (ev){
        if(!ev.isDefaultPrevented) {
            ev.preventDefault()
        }

        let select_element = $($(this).parents().eq(2).find($("select"))[0])
        if (confirm(
            'Are you sure you want to load this sql-file\n\n'+$(select_element).find($("option:selected")).text()+'\n\n into the database?'
        )) {
            $.ajax({
                url: "ajax/load_backup.php",
                method: "POST",
                data: {
                    file: $(select_element).val()
                }, success: function (data) {
                    if ($.inArray("success", Object.keys(data)) !== "-1") {
                        if(data["success"]) {
                            if (confirm('File loaded successfully. refresh page now?')) {
                                window.location.reload()
                            } else {
                                console.log("aborted")
                            }
                        }
                    } else {
                        console.log("failed")
                    }
                }
            })
        } else {
            console.log('backup was not loaded');
        }
    })

    let changes_detected_element = $("#changes_detected")
    $.each($(document).find($("input[type='text'][data-field]")), function (index, element) {



        /*
        $(element).on('change', function () {
            if (typeof $(this).attr("data-old") == "string") {
                if($(this).attr("data-old").length > 0) {
                        console.log("OLD: "+$(this).attr("data-old"))

                        if($(this).attr("data-old") == $(this).val()) {
                            console.log("removing OLD")
                            console.log("old: "+$(this).attr("data-old"))
                            console.log("val: "+$(this).val())
                            console.log("")
                            $.ajax({
                                url: "ajax/set_session.php",
                                type: "POST",
                                data: {
                                    changes_data: parseInt($(this).attr("data-id")),
                                    action: "remove",
                                    field: $(this).attr("data-field"),
                                },
                                statusCode: {
                                    404: function () {
                                        console.log("404")
                                    }
                                }, success: function (data) {

                                    load_preview_table()

                                    console.log(data)
                                }
                            })
                        } else {
                            changes.push($(this).attr("data-id"))
                            $("#save_changes_btn").prop('disabled', '')
                            $("#save_changes_btn").removeAttr('disabled')

                            $("td[data-ident='has_changed'][data-id='" + $(this).attr("data-id") + "']").css({
                                background: '#FF0000'
                            })


                            $.ajax({
                                url: "ajax/set_session.php",
                                type: "POST",
                                data: {
                                    changes_data: parseInt($(this).attr("data-id")),
                                    action: "add",
                                    field: $(this).attr("data-field"),
                                    current_val: $(this).val()
                                },
                                statusCode: {
                                    404: function () {
                                        console.log("404")
                                    }
                                }, success: function (data) {

                                    load_preview_table()

                                    console.log(data)
                                }
                            })
                        }

                } else {
                    changes.push($(this).attr("data-id"))
                    $("#save_changes_btn").prop('disabled', '')
                    $("#save_changes_btn").removeAttr('disabled')

                    $("td[data-ident='has_changed'][data-id='" + $(this).attr("data-id") + "']").css({
                        background: '#FF0000'
                    })


                    $.ajax({
                        url: "ajax/set_session.php",
                        type: "POST",
                        data: {
                            changes_data: parseInt($(this).attr("data-id")),
                            action: "add",
                            field: $(this).attr("data-field"),
                            current_val: $(this).val()
                        },
                        statusCode: {
                            404: function () {
                                console.log("404")
                            }
                        }, success: function (data) {

                            load_preview_table()

                            console.log(data)
                        }
                    })
                }



            } else {
                if ($(this).val() != $(this).attr("data-old")) {
                    console.log("not original")

                    let is_red = $(changes_detected_element).css("background").includes("255, 0, 0")
                    if(!is_red) {
                        $(changes_detected_element).css({"background": "#FF0000"})
                    }
                    if($.inArray($(this).attr("data-id"), changes) == "-1") {
                        changes.push($(this).attr("data-id"))
                        $("#save_changes_btn").prop('disabled', '')
                        $("#save_changes_btn").removeAttr('disabled')

                        $("td[data-ident='has_changed'][data-id='"+$(this).attr("data-id")+"']").css({
                            background: '#FF0000'
                        })


                        $.ajax({
                            url: "ajax/set_session.php",
                            type: "POST",
                            data: {
                                changes_data: parseInt($(this).attr("data-id")),
                                action: "add",
                                field: $(this).attr("data-field"),
                                current_val: $(this).val()
                            },
                            statusCode: {
                                404: function () {
                                    console.log("404")
                                }
                            }, success: function (data) {
                                alert(data)
                            }
                        })

                    }
                    if (changes.length> 0) {
                        $(changes_detected_element).css({"background": "#FF0000"})
                    } else {
                        $(changes_detected_element).css({"background": "#00FF00"})
                    }

                } else {
                    console.log("same!")
                    for(let i = 0; i < changes.length; i++) {
                        if(changes[i] == $(this).attr("data-id")) {
                            removeItemOnce(changes, i)

                            $.ajax({
                                url: "set_session.php",
                                type: "POST",
                                data: {
                                    changes_data: parseInt($(this).attr("data-id")),
                                    field: $(this).attr("data-field"),
                                    action: "remove"
                                },
                                statusCode: {
                                    404: function () {
                                        console.log("404")
                                    }
                                }, success: function (data) {
                                    console.log(data)
                                }
                            })


                            $("td[data-ident='has_changed'][data-id='"+$(this).attr("data-id")+"']").css({
                                background: '#00FF00'
                            })

                        }
                    }
                    if (changes.length> 0) {
                        $(changes_detected_element).css({"background": "#FF0000"})

                    } else {
                        $(changes_detected_element).css({"background": "#00FF00"})
                    }
                }


            }

        })
        */
    })

    $("#save_changes_btn").on('click', function (ev) {
        update_database()
    })

    function getParentNode(element, level = 1) { // 1 - default value (if no 'level' parameter is passed to the function)
        while (level-- > 0) {
            element = element.parentNode;
            if (!element) return null; // to avoid a possible "TypeError: Cannot read property 'parentNode' of null" if the requested level is higher than document
        }
        return element;
    }

    var table = $("#base_table").DataTable({
        "sPaginationType": "full_numbers",
        responsive: true,
        stateSave: false,
        lengthMenu: [
            [100, -1, 50, 25, 10],
            [100, 'All', 50, 25, 10],
        ],
        /*
        "createdRow": function( row, data, dataIndex ) {


        },
        */

        columnDefs: [
            {
                responsivePriority: 10000001,
                targets: 9,
                visible: true

            },
            {
                target: 0,
                visible: false,
                searchable: false,
            },
            {
                target: 4,
                visible: true,
                searchable: true,
            }
        ],
    });
    table.on( 'select', function ( e, dt, type, indexes ) {
        table[ type ]( indexes ).nodes().to$().addClass( 'custom-selected' );
        console.log("sel")
    } );


    window.setTimeout(function () {
        var waiting_ = $(document).find($("div#waiting_div"))
       $(waiting_).remove()
        var page = document.getElementsByTagName('body')[0];
        $(page).css({
            overflow: ''
        })
    },1300)






})
