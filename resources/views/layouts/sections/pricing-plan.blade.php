@extends('layouts.master')
@section('title', 'Pricing Plan')
@section('subtitle', 'MageNative has come up with yet another, groundbreaking product in form of a native, customizable Android/iOS app for Shopify Stores.')
@section('content')
<main class="content-wrapper">
	<div class="container-fluid">
		<div class="pricing-plan">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					<h2 class="pricing-plan__title">Choose a Plan</h2>
					<p>Satisfied with our services! Purchase & continue with any of these plan.</p>
					<nav class="pricing-plan__tabs">
						<div class="nav nav-tabs" id="nav-tab" role="tablist">
							<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
							aria-controls="nav-home" aria-selected="true">Monthly</a>
							<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"
							aria-controls="nav-profile" aria-selected="false">Annaly</a>
						</div>
					</nav>
				</div>
			</div>
			<div class="pricing-plan__box">
				<div class="tab-content" id="nav-tabContent">
					<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

						<div class="row">
							<div class="col-lg-4">
								<div class="pricing-plan-module">
									<h3 class="pricing-plan-module__title">Android</h3>
									<p>Satisfied with our services! Purchase & continue with any of these plan.</p>
									<span class="pricing-plan-module__price">$20</span></span>
									<ul>
										<li>Custom Fields</li>
										<li>Web forms</li>
										<li>Tags and Groups </li>
										<li>Rules and Profiles</li>
									</ul>
									<a href="{{ route('billing', ['plan' => 1]) }}" class="btn">Purchase</a>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="pricing-plan-module">
									<h3 class="pricing-plan-module__title">iOS</h3>
									<p>Satisfied with our services! Purchase & continue with any of these plan.</p>
									<span class="pricing-plan-module__price">$20</span></span>
									<ul>
										<li>Custom Fields</li>
										<li>Web forms</li>
										<li>Tags and Groups </li>
										<li>Rules and Profiles</li>
									</ul>
									<a href="{{ route('billing', ['plan' => 2]) }}" class="btn">Purchase</a>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="pricing-plan-module">
									<h3 class="pricing-plan-module__title">Android and iOS both</h3>
									<p>Satisfied with our services! Purchase & continue with any of these plan.</p>
									<span class="pricing-plan-module__price">$20</span></span>
									<ul>
										<li>Custom Fields</li>
										<li>Web forms</li>
										<li>Tags and Groups </li>
										<li>Rules and Profiles</li>
									</ul>
									<a href="{{ route('billing', ['plan' => 3]) }}" class="btn">Purchase</a>
								</div>
							</div>
						</div>

					</div>
					<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

						<div class="row">
							<div class="col-lg-4">
								<div class="pricing-plan-module">
									<h3 class="pricing-plan-module__title">Android</h3>
									<p>Satisfied with our services! Purchase & continue with any of these plan.</p>
									<span class="pricing-plan-module__price">$20</span></span>
									<ul>
										<li>Custom Fields</li>
										<li>Web forms</li>
										<li>Tags and Groups </li>
										<li>Rules and Profiles</li>
									</ul>
									<a href="{{ route('billing', ['plan' => 4]) }}" class="btn">Purchase</a>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="pricing-plan-module">
									<h3 class="pricing-plan-module__title">iOS</h3>
									<p>Satisfied with our services! Purchase & continue with any of these plan.</p>
									<span class="pricing-plan-module__price">$20</span></span>
									<ul>
										<li>Custom Fields</li>
										<li>Web forms</li>
										<li>Tags and Groups </li>
										<li>Rules and Profiles</li>
									</ul>
									<a href="{{ route('billing', ['plan' => 5]) }}" class="btn">Purchase</a>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="pricing-plan-module">
									<h3 class="pricing-plan-module__title">Android and iOS both</h3>
									<p>Satisfied with our services! Purchase & continue with any of these plan.</p>
									<span class="pricing-plan-module__price">$20</span></span>
									<ul>
										<li>Custom Fields</li>
										<li>Web forms</li>
										<li>Tags and Groups </li>
										<li>Rules and Profiles</li>
									</ul>
									<a href="{{ route('billing', ['plan' => 6]) }}" class="btn">Purchase</a>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection