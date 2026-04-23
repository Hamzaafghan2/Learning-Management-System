@extends('instructor.instructor_dashbord')
@section('instructor')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">

  <div class="row">
					<div class="col-12 ">
						
						<div class="card radius-10">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<img src="{{asset($course->course_image)}}" class="rounded-circle p-1 border" width="90" height="90" alt="...">
									<div class="flex-grow-1 ms-3">
										<h5 class="mt-0">{{$course->course_name}}</h5>
										<p class="mb-0">{{ $course->course_title }}</p>
									</div>
                  	<!-- Button trigger modal -->
										<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add section</button>
								</div>
							</div>
						</div>
						&nbsp;
						{{-- ///Add section and Lecture// --}}
						@foreach ($section as $key => $item )
             <div class="container">
							<div class="main-body">
								<div class="row">
									<div class="col-lg-12">
										<div class="card">
											<div class="card-body p-4 d-flex justify-content-between">
												<h6>{{ $item->section_title }} </h6>
        <div class="d-flex justify-content-between align-items-center">
    <form action="{{ route('delete.section',['id'=> $item->id]) }}" method="POST">
			@csrf
					<button class="btn btn-danger px-2 ms-auto">Delete Section</button> &nbsp;
     </form>
					<a class="btn btn-primary" onclick="addLectureDiv({{ $course->id }},{{ $item->id}},'lectureContainer{{ $key }}')" id="addLectureBtn($key)">Add Lecture</a>
				</div>

											</div>


											<div class="courseHide" id="lectureContainer{{ $key }}">
             <div class="container">
							@foreach ($item->lectures as $lecture)
								
						
               <div class="lectureDiv mb-3 d-flex align-items-center justify-content-between">
            
            <strong>{{ $loop->iteration }}.{{$lecture->lecture_title}}</strong>

			<div class="btn-group">
					<a href="{{ route('edit.lecture',['id'=>$lecture->id]) }}" class="btn btn-sm btn-primary">Edit</a>&nbsp;
					<a href="{{ route('delete.lecture',['id'=>$lecture->id]) }}" class="btn btn-sm btn-danger" id="delete">Delete</a>
			</div>

        </div>
					@endforeach
    </div>
</div>



										</div>


									</div>
								</div>
							</div>
						 </div>
						 	@endforeach
						{{-- ///End Add section and Lecture// --}}
						
					</div>
				</div>

     </div>
     	<!-- Modal -->
										<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="exampleModalLabel">Add Section</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
						<form action="{{ route('add.course.section') }}" method="POST">
									@csrf
							<input type="hidden" name="id" value="{{$course->id }}">

                              <div class="form-group mb-3">
                     <label for="input1" class="form-label">Course Section</label>
                      <input type="text" name="section_title"class="form-control" id="input1" >
                      </div>

                          </div>
													<div class="modal-footer">
														<button type="submit" class="btn btn-primary">Save changes</button>
													</div>
                          </form>
												</div>
											</div>
										</div>
                
							

										 <script>
function addLectureDiv(courseId, sectionId, containerId) {
  const lectureContainer = document.getElementById(containerId);
  const newLectureDiv = document.createElement('div');
  newLectureDiv.classList.add('lectureDiv', 'mb-3');

  newLectureDiv.innerHTML = `
    <div class="container">
      <h6>Lecture Title</h6>
      <input type="text" class="form-control lecture-title" placeholder="Enter Lecture Title">
      <textarea class="form-control mt-2 lecture-content" placeholder="Enter Lecture Content"></textarea>

      <h6 class="mt-2">Video URL</h6>
      <input type="text" class="form-control lecture-url" name="url" placeholder="Add URL">

      <div class="mt-3">
        <button class="btn btn-primary" 
                onclick="saveLecture(${courseId}, ${sectionId}, '${containerId}', this)">
          Save Lecture
        </button>

        <button class="btn btn-secondary" 
                onclick="hideLectureContainer('${containerId}')">
          Cancel
        </button>
      </div>
    </div>
  `;

  lectureContainer.appendChild(newLectureDiv);
  // make sure the container is visible
  lectureContainer.style.display = '';
}

function hideLectureContainer(containerId) {
  const lectureContainer = document.getElementById(containerId);
  if (!lectureContainer) return;
  lectureContainer.style.display = 'none';
}
                  </script>

									<script>
									function saveLecture(courseId, sectionId, containerId, btn) {
										// btn is the Save button element (optional helper)
										const lectureContainer = document.getElementById(containerId);
										if (!lectureContainer) {
											console.error('Lecture container not found:', containerId);
											return;
										}

										// scope the selectors inside this generated block only
										// the latest appended block is the last child of lectureContainer
										const latestBlock = lectureContainer.lastElementChild;
										// fallback: use lectureContainer.querySelector if lastElementChild doesn't exist / structure differs
										const root = latestBlock || lectureContainer;

										const lectureTitleInput = root.querySelector('.lecture-title');
										const lectureContentInput = root.querySelector('.lecture-content');
										const lectureUrlInput = root.querySelector('.lecture-url');

										const lectureTitle = lectureTitleInput ? lectureTitleInput.value.trim() : '';
										const lectureContent = lectureContentInput ? lectureContentInput.value.trim() : '';
										const lectureUrl = lectureUrlInput ? lectureUrlInput.value.trim() : '';

										// basic validation
										if (!lectureTitle) {
											alert('Please enter a lecture title');
											return;
										}

										fetch('{{ url("/save-lecture") }}', {
											method: 'POST',
											headers: {
												'Content-Type': 'application/json',
												'X-CSRF-TOKEN': '{{ csrf_token() }}',
											},
											body: JSON.stringify({
												course_id: courseId,
												section_id: sectionId,
												lecture_title: lectureTitle,
												lecture_url: lectureUrl,
												content: lectureContent,
											}),
										})
										.then(async response => {
											// check HTTP status
											if (!response.ok) {
												const txt = await response.text();
												throw new Error('Network response was not ok: ' + response.status + ' — ' + txt);
											}
											return response.json();
										})
										.then(data => {
											console.log('save-lecture response:', data);

											if (data.success) {
												// show success toast (Swal example you used)
												if (typeof Swal !== 'undefined') {
													Swal.fire({ icon: 'success', title: data.success, timer: 2000, showConfirmButton: false });
												} else {
													alert(data.success);
												}

												// append a visible lecture block right after the form block (without reload)
												const newLecture = document.createElement('div');
												newLecture.classList.add('lectureDiv', 'mb-3');
												newLecture.innerHTML = `
													<div class="d-flex justify-content-between align-items-center p-2 border rounded">
														<div>
															<strong>${escapeHtml(lectureTitle)}</strong><br>
															<small>${escapeHtml(lectureContent)}</small>
														</div>
														<div>
															<a href="#" class="btn btn-sm btn-primary">Edit</a>
															<a href="#" class="btn btn-sm btn-danger">Delete</a>
														</div>
													</div>
												`;

												// append the visible block after the lectureContainer (so it shows in the list)
												lectureContainer.parentElement.appendChild(newLecture);

												// remove the input form block (the one we added). If we used lastElementChild, remove it.
												if (latestBlock) latestBlock.remove();
												else {
													// fallback: clear fields and hide
													if (lectureTitleInput) lectureTitleInput.value = '';
													if (lectureContentInput) lectureContentInput.value = '';
													if (lectureUrlInput) lectureUrlInput.value = '';
													lectureContainer.style.display = 'none';
												}
											} else {
												// server returned errors
												const err = data.error || data.errors || 'Unknown server error';
												console.error(err);
												if (typeof Swal !== 'undefined') {
													Swal.fire({ icon: 'error', title: 'Error', text: JSON.stringify(err) });
												} else {
													alert('Error: ' + JSON.stringify(err));
												}
											}
										})
										.catch(error => {
											console.error('Save lecture failed:', error);
											alert('Save failed — check console for details.');
										});
									}

									// helper to avoid XSS when injecting user text
									function escapeHtml(text) {
										return text
											.replace(/&/g, "&amp;")
											.replace(/</g, "&lt;")
											.replace(/>/g, "&gt;")
											.replace(/"/g, "&quot;")
											.replace(/'/g, "&#039;");
									}
               </script> 


				

									


       @endsection