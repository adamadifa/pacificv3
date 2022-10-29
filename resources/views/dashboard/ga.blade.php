@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <!-- Dashboard Analytics Start -->
        <section id="nav-justified">
            <div class="row">
                <div class="col-sm-4">
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h4 class="card-title">Jatuh Tempo KIR</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">

                                <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab" aria-controls="home-just" aria-selected="true">Bulan Ini</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#profile-just" role="tab" aria-controls="profile-just" aria-selected="false">Bulan Depan</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="messages-tab-justified" data-toggle="tab" href="#messages-just" role="tab" aria-controls="messages-just" aria-selected="false">2 Bulan Lagi</a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content pt-1">
                                    <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified">
                                        <p>
                                            Biscuit powder jelly beans. Lollipop candy canes croissant icing chocolate cake. Cake fruitcake powder
                                            pudding pastry.Danish fruitcake bonbon bear claw gummi bears apple pie. Chocolate sweet topping
                                            fruitcake cake.</p>
                                    </div>
                                    <div class="tab-pane" id="profile-just" role="tabpanel" aria-labelledby="profile-tab-justified">
                                        <p>
                                            Chocolate cake icing tiramisu liquorice toffee donut sweet roll cake. Cupcake dessert icing dragée
                                            dessert. Liquorice jujubes cake tart pie donut. Cotton candy candy canes lollipop liquorice chocolate
                                            marzipan muffin pie liquorice.
                                        </p>
                                    </div>
                                    <div class="tab-pane" id="messages-just" role="tabpanel" aria-labelledby="messages-tab-justified">
                                        <p>
                                            Tootsie roll oat cake I love bear claw I love caramels caramels halvah chocolate bar. Cotton candy
                                            gummi bears pudding pie apple pie cookie. Cheesecake jujubes lemon drops danish dessert I love
                                            caramels powder.
                                        </p>
                                    </div>
                                    <div class="tab-pane" id="settings-just" role="tabpanel" aria-labelledby="settings-tab-justified">
                                        <p>
                                            Biscuit powder jelly beans. Lollipop candy canes croissant icing chocolate cake. Cake fruitcake powder
                                            pudding pastry.I love caramels caramels halvah chocolate bar. Cotton candy
                                            gummi bears pudding pie apple pie cookie.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
