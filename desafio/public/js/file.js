let page = 1;
let fileRecordsPage = 1;
let interval = null;
let fileId = null;

document.addEventListener('DOMContentLoaded', async function () {
    if(await loadFiles(appendFile)) {
        updateFiles();
    }

    document.getElementById('add-file-form').addEventListener('submit', async function (event) {
        event.preventDefault();

        const form = event.target;
        const file = form.querySelector('input[type="file"]').files[0];

        const formData = new FormData();
        formData.append('name', form.querySelector('input[type="text"]').value);
        formData.append('file', file);

        const response = await api.post('/files', formData);

        if(!interval) {
            updateFiles();
        }

        if(!response?.ok) {
            alert(response?.data?.message || 'Algo deu errado ao adicionar o arquivo. Tente novamente mais tarde!');
            return;
        }

        appendFile(response.data.data);

        form.reset();
        document.querySelector('#add-file-modal button[data-bs-dismiss="modal"]').click();
    });
});

async function loadFiles(callback)
{
    let authenticated = true;

    await api.get(`/files?page=${page}`).then(({ status, data }) => {
        if(status === 401) {
            authenticated = false;
            document.getElementById('login-btn').click();
            return;
        }

        mountPagination(data.meta.links, document.getElementById('pagination-container'), page, goToPage);

        if(callback) {
            data.data.forEach(callback);
        }
    });

    return authenticated;
}

function mountPagination(links, paginationContainer, currentPage, callback)
{
    const template = document.getElementById('pagination-template').content;

    const previous = paginationContainer.querySelector('li:first-child button');
    const next = paginationContainer.querySelector('li:last-child button');

    if(links.shift().url === null) {
        previous.classList.add('disabled');
    } else {
        previous.classList.remove('disabled');
        previous.onclick = () => callback(currentPage - 1);
    }

    if(links.pop().url === null) {
        next.classList.add('disabled');
    } else {
        next.classList.remove('disabled');
        next.onclick = () => callback(currentPage + 1);
    }

    paginationContainer.querySelectorAll('.page-item-page').forEach(page => page.remove());

    links.forEach(({ label, active }) => {
        const clone = template.cloneNode(true);

        const button = clone.querySelector('button');

        button.textContent = label;

        if(active) {
            button.classList.add('active', active);
        } else if(label !== '...') {
            button.classList.remove('active', active);
            button.addEventListener('click', () => callback(parseInt(label)));
        }

        paginationContainer.querySelector('ul').insertBefore(clone, next.parentElement);
    });

    paginationContainer.classList.remove('d-none');
}

function goToPage(nextPage)
{
    page = nextPage;

    document.querySelectorAll('.file-container:not(.add-file)').forEach(file => file.parentElement.remove());

    loadFiles(appendFile);
}

function goToFileRecordPage(nextPage)
{
    fileRecordsPage = nextPage;
    loadFileRecords(fileId);
}

function appendFile(file)
{
    const filesContainer = document.querySelector('.files-container');
    const template = document.getElementById('file-card-template').content;
    const clone = template.cloneNode(true);

    clone.querySelector('.file-container').setAttribute('data-id', file.id);
    clone.querySelector('.file-container').setAttribute('onclick', `loadFileRecords('${file.id}')`);
    clone.querySelector('.file-name').textContent = file.name;
    clone.querySelector('.file-info-value-link a').setAttribute('href', file.download_path);
    clone.querySelector('.file-info-value-link a').setAttribute('download', file.name);
    clone.querySelector('.file-info-value-type').textContent = file.extension.toUpperCase();
    clone.querySelector('.file-info-value-size').textContent = fileSize(file.size);
    clone.querySelector('.file-info-value-status').textContent = fileStatus(file.status, file.progress);
    clone.querySelector('.file-info-value-records').textContent = new Intl.NumberFormat(getLocale()).format(file.records || 0);
    clone.querySelector('.file-info-value-createdAt').textContent = new Date(file.created_at).toLocaleString(getLocale());
    clone.querySelector('.progress-bar').style.width = formatPercent(file.progress, 'en');

    if(file.progress === 100) {
        clone.querySelector('.file-info-value-status').textContent = fileStatus('uploaded');
        clone.querySelector('.progress-bar').classList.add('uploaded');
    }

    filesContainer.insertBefore(clone, document.querySelector('.file-container.add-file').parentElement);
}

function updateFiles()
{
    clearInterval(interval);

    interval = setInterval(() => {
        loadFiles(({ id, status, progress, records, ...rest }) => {
            const file = document.querySelector(`.file-container[data-id="${id}"]`);

            if(!file) {
                appendFile({ id, status, progress, records, ...rest });
                return;
            }

            file.querySelector('.file-info-value-status').textContent = fileStatus(status, progress);
            file.querySelector('.file-info-value-records').textContent = new Intl.NumberFormat(getLocale()).format(records || 0);
            file.querySelector('.progress-bar').style.width = formatPercent(progress, 'en');

            if(progress === 100) {
                file.querySelector('.file-info-value-status').textContent = fileStatus('uploaded');
                file.querySelector('.progress-bar').classList.add('uploaded');
            }
        });
    }, 5000);
}

function loadFileRecords(selectedFileId)
{
    api.get(`/files/${selectedFileId}/content?page=${fileRecordsPage}`).then(({ status, data }) => {
        if(status !== 200) {
            alert(data?.message || 'Algo deu errado ao carregar os registros. Tente novamente mais tarde!');
            return;
        }

        fileId = selectedFileId;

        document.querySelectorAll('.file-container').forEach(file => file.classList.remove('active'));
        document.querySelector(`.file-container[data-id="${selectedFileId}"]`).classList.add('active');

        const recordsContainer = document.getElementById('file-records-container');
        recordsContainer.classList.remove('d-none');

        const recordsTable = document.getElementById('file-records-table');
        const template = document.getElementById('file-record-row').content;

        if(data.meta?.links) {
            mountPagination(data.meta.links, document.getElementById('file-records-pagination-container'), fileRecordsPage, goToFileRecordPage);
        } else {
            document.getElementById('file-records-pagination-container').classList.add('d-none');
        }

        recordsTable.querySelectorAll('tbody tr:not(#file-records-table-empty)').forEach(tr => tr.remove());
        document.getElementById('file-records-table-empty').classList.add('d-none');

        if(data.data.length) {
            data.data.forEach(record => {
                const clone = template.cloneNode(true);

                Object.entries(record).forEach(([key, value]) => {
                    const td = clone.querySelector(`td[data-key="${key}"]`);

                    // debugger;
                    if(!td) return;
                    td.innerText = value || '-';
                });

                recordsTable.querySelector('tbody').appendChild(clone);
            });
        } else {
            document.getElementById('file-records-table-empty').classList.remove('d-none');
        }

    });
}

function formatPercent(value = 0, locale = getLocale())
{
    return new Intl.NumberFormat(locale, {
        style: 'percent',
        maximumFractionDigits: 2
    }).format(value / 100);
}

function fileSize(bytes, unit = 'MB')
{
    unit = unit.toUpperCase();

    let size = bytes;

    switch(unit) {
        case 'GB':
            size /= 1024;
        case 'MB':
            size /= 1024;
        case 'KB':
            size /= 1024;
        break;
        default:
            throw new Error('Invalid unit');
    }

    return new Intl.NumberFormat(getLocale(), {
        style: 'decimal',
        maximumFractionDigits: 1
    }).format(size).concat(` ${unit}`);
}

function fileStatus(status, progress)
{
    return {
        pending: 'Pendente',
        uploading: `Processando (${formatPercent(progress)})`,
        uploaded: 'Concluído',
    }[status] || status;
}
