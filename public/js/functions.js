function submitAjax(form)
{
    $.ajax({
        url: $(form).attr('action'),
        type: $(form).attr('method')
        //data: { type: $('subType').value, id: $('subId').value },
        //dataType: "json"
    })
        .done(function(msg) {
            msg = JSON.parse(msg);
            if(msg.success)
            {
                $('#flash').prepend(msg.message);
            }
            else $('#flash').prepend(msg.message);
        });
}

function highlightCorrect()
{
    var ca = $('.correct-answer').toArray();
    ca.forEach(function(a) {
        var v = $(a)[0].value;
        var correct = $(a).next().children('li')[v - 1];
        $(correct).addClass('correct');
    });
}

function pad2(number) {
    return (number < 10 ? '0' : '') + number
}

function getTotalTime(sH, eH, sM, eM) {
    var t = (eH - sH) * 60 + (eM - sM);
    console.log(t);
    return t;
}
