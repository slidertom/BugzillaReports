class gantt_tooltips
{
    static create_bug_tooltip(item_id, bug_id_text, bug_data)
    {
        const title           = "#" + bug_id_text + ": "      +bug_data.summary;
        const reporter_div    = "<div>Reporter:       "+bug_data.reporter+"</div>";
        const remain_time_div = "<div>Remaining time: "+bug_data.remain_time+" h</div>";
        const priority_div    = "<div>Priority:       "+bug_data.priority+"</div>";
        const severity_div    = "<div>Severity:       "+bug_data.severity+"</div>";
        const complete_div    = "<div>Completed:      "+bug_data.complete+"</div>";
        const worked_div      = "<div>Worked:         "+bug_data.worked_time+" h</div>";
        
        const content = priority_div + severity_div + reporter_div + remain_time_div+worked_div+complete_div;
        // now just create a tooltip
        const bug_item = $(item_id);
        bug_item.addTip(content, title, { target: true, stem: true, tipJoint: [ "left", "middle" ], style: 'grouped', showOn: "creation", showEffect: 'appear' });
        // next time please show tooltip on mouseover
        bug_item.addTip(content, title, { target: true, stem: true, tipJoint: [ "left", "middle" ], style: 'grouped', showEffect: 'appear' });
    }

    static show_tooltip(bug_id, obj_id)
    {
        const values = "bug_id="+bug_id;
        jsonPostSync("/_Bugzilla/ajax_json_get_bug_info.php?"+values, "", function(bug_data)  {   
            gantt_tooltips.create_bug_tooltip(obj_id, bug_id, bug_data);
        });
    }

    static mouse_over()
    {
        const jquery_obj = jQuery(this);
        const child = jquery_obj.find(".fn-label").first();
        if ( !child ) {
            return; 
        }
        const str_id = jquery_obj.attr("id");
        if ( typeof str_id !== "undefined" ) {
            return;
        }
        const bug_id = child.text();

        const item_id = "bug_" + bug_id;
        child.attr("id", item_id);
        gantt_tooltips.show_tooltip(bug_id, item_id);
    }

    static init_tooltips()
    {
        const items = document.querySelectorAll('.GanttBug');
        let i;
        for (i = 0; i < items.length; ++i) {
            const gantt_item = items[i];
            gantt_item.addEventListener("mouseover", gantt_tooltips.mouse_over);    
        }    
    }
}