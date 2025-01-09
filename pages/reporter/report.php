<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Report.php';
require_once '../../classes/Notification.php';
require_once '../../includes/config.php';
include '../../includes/header.php';
?>

<div class="container my-5">
    <!-- Progress Bar -->
    <div class="progress mb-4">
        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Report a Crime</h4>
        </div>
        <div class="card-body">
            <form id="crimeReportForm" action="process_report.php" method="POST" enctype="multipart/form-data">
                <!-- Crime Type -->
                <div class="mb-3">
                    <label class="form-label">Crime Type</label>
                    <select class="form-select" name="crime_type" required>
                        <option value="">Select crime type</option>
                        <option value="theft">Theft</option>
                        <option value="assault">Assault</option>
                        <option value="kidnapping">Kidnapping</option>
                        <option value="banditry">Banditry</option>
                        <option value="terrorism">Terrorism</option>
                        <option value="vandalism">Vandalism</option>
                        <option value="robbery">Robbery</option>
                        <option value="human_trafficking">Human Trafficking</option>
                        <option value="drug_trafficking">Drug Trafficking</option>
                        <option value="murder">Murder</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="feedback"></div>
                </div>

                <!-- Crime Description -->
                <div class="mb-3">
                    <label class="form-label">Crime Description</label>
                    <textarea class="form-control" name="description" rows="4" required></textarea>
                    <div class="feedback"></div>
                </div>

                <!-- Location Details -->
                <div class="mb-3">
                    <label class="form-label">Local Government Area</label>
                    <select class="form-select" name="lga" required>
                        <option value="">Select LGA</option>
                        <option value="anka">Anka</option>
                        <option value="bakura">Bakura</option>
                        <option value="birnin_magaji">Birnin Magaji</option>
                        <option value="bukkuyum">Bukkuyum</option>
                        <option value="bungudu">Bungudu</option>
                        <option value="gummi">Gummi</option>
                        <option value="gusau">Gusau</option>
                        <option value="kaura_namoda">Kaura Namoda</option>
                        <option value="maradun">Maradun</option>
                        <option value="maru">Maru</option>
                        <option value="shinkafi">Shinkafi</option>
                        <option value="talata_mafara">Talata Mafara</option>
                        <option value="tsafe">Tsafe</option>
                        <option value="zurmi">Zurmi</option>
                    </select>
                    <div class="feedback"></div>
                </div>

                <!-- Specific Location -->
                <div class="mb-3">
                    <label class="form-label">Specific Location</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="location" id="location" required>
                        <button type="button" class="btn btn-secondary" id="getLocation">
                            <i class="fas fa-map-marker-alt"></i> Auto-detect
                        </button>
                    </div>
                    <div id="mapPreview" class="map-container mt-2" style="height: 200px;"></div>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                </div>

                <!-- Date and Time -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Crime</label>
                        <input type="date" class="form-control" name="crime_date" required>
                        <div class="feedback"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Time of Crime</label>
                        <input type="time" class="form-control" name="crime_time" required>
                        <div class="feedback"></div>
                    </div>
                </div>

                <!-- Enhanced File Upload -->
                <div class="mb-3">
                    <label class="form-label">Evidence Files (Optional)</label>
                    <div class="file-upload-wrapper">
                        <div class="upload-zone" id="dropZone">
                            <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                            <p>Drag and drop files here or click to browse</p>
                            <input type="file" class="file-input" name="evidence[]" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx">
                        </div>
                        <div class="file-preview" id="filePreview"></div>
                    </div>
                </div>

                <!-- Voice Recording -->
                <div class="mb-3">
                    <label class="form-label">Voice Report (Optional)</label>
                    <div class="voice-recorder">
                        <canvas id="waveform"></canvas>
                        <div class="controls">
                            <button type="button" id="startRecording" class="btn btn-primary">
                                <i class="fas fa-microphone"></i> Start Recording
                            </button>
                            <button type="button" id="stopRecording" class="btn btn-danger d-none">
                                <i class="fas fa-stop"></i> Stop
                            </button>
                        </div>
                        <audio id="audioPlayback" controls class="d-none w-100 mt-2"></audio>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add these scripts before closing body tag -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="<?php echo BASE_PATH; ?>assets/js/report.js"></script>

<?php include '../../includes/footer.php'; ?>
