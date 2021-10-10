let newStatusForFilter = '';
const backgroundColor =["#FF0000", "#008000", "#FFA500", "#800000"];
$.fn.DataTable.ext.pager.numbers_length = 17;
(async function (params) {
    $('#date-range').datepicker({ toggleActive: true });
    $('.select2.select2-container.select2-container').addClass('w-100');

    let statuses = await $.ajax({ url: '/api/leads/count-status' });
    let labels = statuses.map(obj => obj.status_caller);
    let total = statuses.map(obj => obj.total);
  
    renderNewChart();
    generateStats(statuses,total);
    renderPieChart(labels, total, backgroundColor);
    
})();
const table = $('#report_datatable').DataTable({
    processing: true,
    serverSide: true,
    searching: false,
    order: [[ 0, "desc" ]],
    ajax: {
        url: '/api/reports/get',
        data: function (d) {
            d.role = $('#role').val();
            d.from = $('#startDate').val();
            d.to = $('#endDate').val();
            d.caller_filter = $('#callerfilter').val();
        }
    },
    columns: [
        {data: 'id', name: 'id'},
        {data: 'order_id', name: 'order_id'},
        {data: 'customer_id', name: 'customer_id'},
        {data: 'caller_id', name: 'caller_id'},
        {data: 'product_id', name: 'product_id'},
        {data: 'price', name: 'price'},
        {data: 'quantity', name: 'quantity'},
        {data: 'note', name: 'note'},
        {data: 'created_at', name: 'created_at'},
        {data: 'status_caller', name: 'status_caller'},
        
    ],
  //   dom: 'Bfrtip',
  // buttons: [
  // 'copy', 'csv', 'excel', 'pdf', 'print'
  // ],

    "fnDrawCallback": function(){
        $('#gotoPageNumber').val('');
        handleLongPagination();
        handleChangeStatus();
    }
    
});
$.fn.DataTable.Api.register( 'buttons.exportData()', function( options ) {
    if(this.context.length) {

      var src_keyword = $('.dataTables_filter input').val();

      // make columns for sorting
      var columns = [];
      $.each(this.context[0].aoColumns, function(key, value) {
        columns.push({
          'data' : value.data,
          'name' : value.name,
          'searchable' : value.bSearchable,
          'orderable' : value.bSortable
        });
      });

      // make option for sorting
      var order = [];
      $.each(this.context[0].aaSorting, function(key, value) {
        order.push({
          'column' : value[0],
          'dir' : value[1]
        });
      });

      // make value for search
      var search = {
        'value' : this.context[0].oPreviousSearch.sSearch,
        'regex' : this.context[0].oPreviousSearch.bRegex
      };

      var items = [];
      var status = $('#status').val();
      $.ajax({
        url: "/api/reports/get",
        // data: { columns: columns, order: order, search: search, status: status, page: 'all' },
        success: function (result) {
            console.log(result);

          $.each(result.data, function(key, value) {

            var item = [];

            item.push(key+1);
            item.push(value.username);
            item.push(value.email);
            item.push(value.created_at);
            item.push(value.status);

            items.push(item);
          });
        },
        async: false
      });

      return {
        body: items,
        // skip actions header
        header: $("#report_datatable thead tr th").map(function() {
          if(this.innerHTML!='Actions')
            return this.innerHTML;
        }).get()
      };
    }
  });

$('.filter-submit').on('click', async function (e) {
    e.preventDefault();
    const startDate = $('#start').val();
    const endDate = $('#end').val();
    const callerFilter = $('#callerfilter').val();

    let statuses = await $.post('/api/callers/filter-search', {
        startDate,
        endDate,
        callerFilter
    });
    if (statuses.length === 0) {
        renderNewChart();
        [...document.querySelectorAll('#stats ul li')].map(elem => elem.remove())
        document.querySelector('.chart-div').innerHTML = `<h1>Nothing Found</h1>`;
        return;
    }

    let labels = statuses.map(obj => obj.status_caller);
    let total = statuses.map(obj => obj.total);


    renderNewChart();
    generateStats(statuses,total);
    renderPieChart(labels, total, backgroundColor);
    generateDataTable();
});

async function generateDataTable(){
    const height = window.pageYOffset;
    
    table.ajax.reload(function () {
        $('.loadingio-spinner-spinner-e1xmlecchsl').hide();

        window.scrollTo(0, height);
    }, false);
}


function handleLongPagination() {
    document.querySelector('#report_datatable_next').addEventListener('click',function(){
        table.page(table.page() + 14).draw(false)
    });
}
function handleChangeStatus() {
    [...document.querySelectorAll('#changeStatus')].map(elem => {
        elem.addEventListener('click', async function (e) {
            e.preventDefault();
            const height = window.pageYOffset;
            $('.loadingio-spinner-spinner-e1xmlecchsl').show();

            let lead = await $.ajax({
                method: 'GET',
                url: '/api/leads/changeStatus',
                data: {
                    leadId: e.currentTarget.dataset.leadid,
                    status: e.currentTarget.dataset.status,
                    role: role
                }
            });
            table.ajax.reload(function () {
                $('.loadingio-spinner-spinner-e1xmlecchsl').hide();

                window.scrollTo(0, height);
            }, false);
        })
    });
}

function renderNewChart() {
    $('#pie-chart').remove();
    $('.chartjs-size-monitor').remove();
    let newChart = document.createElement('canvas');
    newChart.id = 'pie-chart';
    document.querySelector('.chart-div').innerHTML = newChart.outerHTML;
}

function renderPieChart(labels, total , backgroundColor) {
    new Chart(document.getElementById("pie-chart"), {
        type: 'pie',
        data: {
            labels: labels.map(label => label.replace(/\b\w/, v => v.toUpperCase())),
            datasets: [{
                label: "Leads Status",
                backgroundColor: backgroundColor,
                data: total
            }]
        }
    });
}

function generateStats(statuses , total ) {
    let allTotals = total.reduce((a, b) => a + b, 0);
    let stat = statuses.map(obj => `<li>${Math.ceil(obj.total/allTotals *100)}% ${obj.status_caller}</li>`);
    document.querySelector('#stats ul').innerHTML = stat.join("");
}

function handleFilteringSearch() {
    $('.filter-search-submit').on('click', function (e) {
      e.preventDefault();
        from = $("#startDate").val();
        to = $("#endDate").val();
        phone = $('#phone').val();
        orderId = $('#orderId').val();
        caller_filter = $('#callerfilter').val(); 

        if ((from && to) || status || phone || orderId || caller_filter) {
            $('.loadingio-spinner-spinner-e1xmlecchsl').show();

            table.on('draw', function () {
                $('.loadingio-spinner-spinner-e1xmlecchsl').hide();
            });
            table.draw();
        }
    });

    $('.statuses #changeStatus').on('click', function (e) {
        e.preventDefault();
        newStatusForFilter = e.currentTarget.dataset.status;

        $('.loadingio-spinner-spinner-e1xmlecchsl').show();

        table.on('draw', function () {
            $('.loadingio-spinner-spinner-e1xmlecchsl').hide();
        });
        table.draw();
    });
}


window.addEventListener('load', function () {
    $('.select2.select2-container.select2-container').addClass('w-100');

    handleFilteringSearch();

    $('.loadingio-spinner-spinner-e1xmlecchsl').hide();
});

// $(document).ready(function(){
    // (function newexportaction)();
// });
