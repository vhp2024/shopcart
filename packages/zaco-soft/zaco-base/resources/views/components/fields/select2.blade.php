@component('zaco-base::components.forms.input_group', compact('field'))
    @include('zaco-base::components.fields._select')
@endcomponent


@push('script')
    <script>
        $('.select2').select2();
        // var DEFAULT_OPTIONS = [
        //     { id: 'def1', text: 'Default Choice 1' },
        // ];

        // var AJAX_OPTIONS = [
        //     { id: '1', text: 'Choice 1' },
        // ];
        // var lastOptions = DEFAULT_OPTIONS;

        // $(".js-example-data-ajax").select2({
        //     query: function(options) {
        //         console.log('2021-05-30 16:15:55---', options);
        //         if (options.term) {
        //             $.ajax({
        //                 type: 'post',
        //                 url: '/echo/json/',
        //                 dataType: 'json',
        //                 data: {
        //                     json: JSON.stringify(AJAX_OPTIONS),
        //                     delay: 0.3
        //                 },
        //                 success: function(data) {
        //                     lastOptions = data;
        //                     options.callback({ results: data });
        //                 }
        //         });
        //         } else {
        //             options.callback({ results: lastOptions });
        //         }
        //     },
        //     // ajax: {
        //     //     url: "https://api.github.com/search/repositories",
        //     //     dataType: 'json',
        //     //     delay: 250,
        //     //     data: function (params) {
        //     //         return {
        //     //             q: params.term, // search term
        //     //             page: params.page
        //     //         };
        //     //     },
        //     //     processResults: function (data, params) {
        //     //         // parse the results into the format expected by Select2
        //     //         // since we are using custom formatting functions we do not need to
        //     //         // alter the remote JSON data, except to indicate that infinite
        //     //         // scrolling can be used
        //     //         params.page = params.page || 1;

        //     //         return {
        //     //             results: data.items,
        //     //             pagination: {
        //     //             more: (params.page * 30) < data.total_count
        //     //             }
        //     //         };
        //     //     },
        //     //     cache: true
        //     // },
        //     placeholder: 'Search for a repository',
        //     templateResult: formatRepo,
        //     templateSelection: formatRepoSelection
        // });

        // function formatRepo (repo) {
        //     if (repo.loading) {
        //         return repo.text;
        //     }

        //     var $container = $(
        //         "<div class='select2-result-repository clearfix'>" +
        //         "<div class='select2-result-repository__avatar'><img src='" + repo.owner.avatar_url + "' /></div>" +
        //         "<div class='select2-result-repository__meta'>" +
        //             "<div class='select2-result-repository__title'></div>" +
        //             "<div class='select2-result-repository__description'></div>" +
        //             "<div class='select2-result-repository__statistics'>" +
        //             "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> </div>" +
        //             "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> </div>" +
        //             "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> </div>" +
        //             "</div>" +
        //         "</div>" +
        //         "</div>"
        //     );

        //     $container.find(".select2-result-repository__title").text(repo.full_name);
        //     $container.find(".select2-result-repository__description").text(repo.description);
        //     $container.find(".select2-result-repository__forks").append(repo.forks_count + " Forks");
        //     $container.find(".select2-result-repository__stargazers").append(repo.stargazers_count + " Stars");
        //     $container.find(".select2-result-repository__watchers").append(repo.watchers_count + " Watchers");

        //     return $container;
        // }

        // function formatRepoSelection (repo) {
        //     return repo.full_name || repo.text;
        // }
    </script>
@endpush
