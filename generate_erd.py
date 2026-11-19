import matplotlib.pyplot as plt
import matplotlib.patches as patches

fig, ax = plt.subplots(figsize=(24, 15), dpi=300)
ax.set_facecolor('#0f172a')
fig.patch.set_facecolor('#0f172a')

# Title
ax.text(12, 14.3, "DIAGRAM RELASI ENTITAS (ERD) - SISTEM INFORMASI PEMBAYARAN SPP ONLINE", 
        color="#38bdf8", fontsize=18, fontweight='bold', ha='center', va='center')
ax.text(12, 13.9, "Standar Normalisasi 1NF, 2NF, 3NF - Berbasis Laravel & Midtrans Payment Gateway", 
        color="#94a3b8", fontsize=12, ha='center', va='center')

# Color palette
TABLE_HEADER_BG = '#1e293b'
TABLE_BODY_BG = '#1e293b'
PK_COLOR = '#fbbf24' # Gold
FK_COLOR = '#38bdf8' # Cyan
ATTR_COLOR = '#f8fafc' # White
BORDER_COLOR = '#475569'

tables = {
    'users': {
        'pos': (1, 10.2, 3.2, 3.0),
        'title': 'users',
        'color': '#3b82f6',
        'fields': [
            ('PK', 'id_user', 'BIGINT AUTO'),
            ('', 'username', 'VARCHAR(100)'),
            ('', 'email', 'VARCHAR(100) UQ'),
            ('', 'password', 'VARCHAR(255)'),
            ('', 'role', 'ENUM(admin, bendahara, ortu, siswa)'),
            ('', 'nomor_hp', 'VARCHAR(20)'),
            ('', 'is_active', 'BOOLEAN')
        ]
    },
    'orang_tua': {
        'pos': (5.2, 10.2, 3.2, 2.7),
        'title': 'orang_tua',
        'color': '#8b5cf6',
        'fields': [
            ('PK', 'id_orang_tua', 'BIGINT AUTO'),
            ('FK', 'id_user', 'BIGINT'),
            ('', 'nama_wali', 'VARCHAR(100)'),
            ('', 'pekerjaan', 'VARCHAR(100)'),
            ('', 'alamat', 'TEXT'),
            ('', 'no_telepon', 'VARCHAR(20)')
        ]
    },
    'siswa_orang_tua': {
        'pos': (9.4, 10.2, 3.2, 2.2),
        'title': 'siswa_orang_tua',
        'color': '#ec4899',
        'fields': [
            ('PK', 'id_siswa_orang_tua', 'BIGINT AUTO'),
            ('FK', 'id_siswa', 'BIGINT'),
            ('FK', 'id_orang_tua', 'BIGINT'),
            ('', 'status_hubungan', 'VARCHAR(50)')
        ]
    },
    'siswa': {
        'pos': (13.6, 9.8, 3.2, 3.4),
        'title': 'siswa',
        'color': '#10b981',
        'fields': [
            ('PK', 'id_siswa', 'BIGINT AUTO'),
            ('FK', 'id_user', 'BIGINT NULL'),
            ('FK', 'id_kelas', 'INT'),
            ('', 'nis', 'VARCHAR(20) UQ'),
            ('', 'nisn', 'VARCHAR(20) UQ'),
            ('', 'nama_lengkap', 'VARCHAR(100)'),
            ('', 'jenis_kelamin', 'ENUM(L, P)'),
            ('', 'tempat_lahir', 'VARCHAR(50)'),
            ('', 'tanggal_lahir', 'DATE'),
            ('', 'status_siswa', 'ENUM(aktif, lulus, pindah)')
        ]
    },
    'kelas': {
        'pos': (18, 10.5, 3.0, 2.2),
        'title': 'kelas',
        'color': '#14b8a6',
        'fields': [
            ('PK', 'id_kelas', 'INT AUTO'),
            ('', 'nama_kelas', 'VARCHAR(50)'),
            ('', 'tingkat', 'ENUM(7, 8, 9)'),
            ('', 'wali_kelas', 'VARCHAR(100)')
        ]
    },
    'tahun_ajaran': {
        'pos': (18, 6.8, 3.0, 2.2),
        'title': 'tahun_ajaran',
        'color': '#f59e0b',
        'fields': [
            ('PK', 'id_tahun_ajaran', 'INT AUTO'),
            ('', 'tahun_ajaran', 'VARCHAR(10)'),
            ('', 'semester', 'ENUM(ganjil, genap)'),
            ('', 'is_active', 'BOOLEAN')
        ]
    },
    'jenis_tagihan': {
        'pos': (18, 3.2, 3.0, 2.2),
        'title': 'jenis_tagihan',
        'color': '#eab308',
        'fields': [
            ('PK', 'id_jenis_tagihan', 'INT AUTO'),
            ('', 'kode_tagihan', 'VARCHAR(20) UQ'),
            ('', 'nama_tagihan', 'VARCHAR(100)'),
            ('', 'tipe_periode', 'ENUM(bulanan, bebas)')
        ]
    },
    'tarif_tagihan': {
        'pos': (13.6, 5.2, 3.2, 2.6),
        'title': 'tarif_tagihan',
        'color': '#06b6d4',
        'fields': [
            ('PK', 'id_tarif', 'BIGINT AUTO'),
            ('FK', 'id_jenis_tagihan', 'INT'),
            ('FK', 'id_tahun_ajaran', 'INT'),
            ('FK', 'id_kelas', 'INT NULL'),
            ('', 'nominal_tarif', 'DECIMAL(12,2)')
        ]
    },
    'tagihan': {
        'pos': (13.6, 0.8, 3.4, 3.4),
        'title': 'tagihan',
        'color': '#f97316',
        'fields': [
            ('PK', 'id_tagihan', 'BIGINT AUTO'),
            ('FK', 'id_siswa', 'BIGINT'),
            ('FK', 'id_tarif', 'BIGINT'),
            ('', 'bulan', 'TINYINT NULL (1-12)'),
            ('', 'tahun', 'YEAR NULL'),
            ('', 'nominal_tagihan', 'DECIMAL(12,2)'),
            ('', 'nominal_terbayar', 'DECIMAL(12,2)'),
            ('', 'status_tagihan', 'ENUM(belum, sebagian, lunas)'),
            ('', 'jatuh_tempo', 'DATE')
        ]
    },
    'detail_pembayaran': {
        'pos': (8.8, 1.2, 3.4, 2.4),
        'title': 'detail_pembayaran',
        'color': '#d946ef',
        'fields': [
            ('PK', 'id_detail_pembayaran', 'BIGINT AUTO'),
            ('FK', 'id_pembayaran', 'BIGINT'),
            ('FK', 'id_tagihan', 'BIGINT'),
            ('', 'nominal_bayar', 'DECIMAL(12,2)')
        ]
    },
    'pembayaran': {
        'pos': (4.2, 0.8, 3.6, 3.6),
        'title': 'pembayaran (Transaksi Gateway)',
        'color': '#ef4444',
        'fields': [
            ('PK', 'id_pembayaran', 'BIGINT AUTO'),
            ('FK', 'id_user', 'BIGINT (Pembayar)'),
            ('', 'order_id', 'VARCHAR(64) UQ'),
            ('', 'gross_amount', 'DECIMAL(12,2)'),
            ('', 'payment_type', 'VARCHAR(50)'),
            ('', 'transaction_status', 'ENUM(pending, paid, expire, cancel, failed)'),
            ('', 'snap_token', 'VARCHAR(255)'),
            ('', 'paid_at', 'DATETIME NULL'),
            ('', 'pdf_kuitansi_url', 'VARCHAR(255)')
        ]
    },
    'notifikasi_gateway': {
        'pos': (0.2, 1.2, 3.2, 2.6),
        'title': 'notifikasi_gateway (Webhook Logs)',
        'color': '#64748b',
        'fields': [
            ('PK', 'id_notifikasi', 'BIGINT AUTO'),
            ('FK', 'id_pembayaran', 'BIGINT'),
            ('', 'order_id', 'VARCHAR(64)'),
            ('', 'status_code', 'VARCHAR(10)'),
            ('', 'raw_response', 'JSON'),
            ('', 'created_at', 'TIMESTAMP')
        ]
    }
}

def draw_table(ax, info):
    x, y, w, h = info['pos']
    title = info['title']
    header_color = info['color']
    fields = info['fields']
    
    # Shadow
    shadow = patches.FancyBboxPatch((x+0.05, y-0.05), w, h, boxstyle="round,pad=0.03", 
                                    fc='#030712', ec='none', zorder=1)
    ax.add_patch(shadow)
    
    # Card background
    card = patches.FancyBboxPatch((x, y), w, h, boxstyle="round,pad=0.03", 
                                  fc='#1e293b', ec=header_color, lw=1.5, zorder=2)
    ax.add_patch(card)
    
    # Header bar
    hh = 0.45
    header = patches.Rectangle((x, y + h - hh), w, hh, fc=header_color, ec='none', zorder=3)
    ax.add_patch(header)
    ax.text(x + w/2, y + h - hh/2, title.upper(), color='#ffffff', fontsize=9.5, 
            fontweight='bold', ha='center', va='center', zorder=4)
    
    # Fields
    curr_y = y + h - hh - 0.22
    line_h = (h - hh - 0.1) / len(fields)
    for key_type, name, type_info in fields:
        # Key badge
        if key_type == 'PK':
            ax.text(x + 0.15, curr_y, 'PK', color=PK_COLOR, fontsize=7.5, fontweight='bold', zorder=4)
        elif key_type == 'FK':
            ax.text(x + 0.15, curr_y, 'FK', color=FK_COLOR, fontsize=7.5, fontweight='bold', zorder=4)
        else:
            ax.text(x + 0.15, curr_y, '  ', color='#64748b', fontsize=7.5, zorder=4)
            
        ax.text(x + 0.55, curr_y, name, color=ATTR_COLOR, fontsize=8, fontweight='medium', zorder=4)
        ax.text(x + w - 0.15, curr_y, type_info, color='#94a3b8', fontsize=7, ha='right', zorder=4)
        curr_y -= line_h

for name, info in tables.items():
    draw_table(ax, info)

def draw_arrow(p1, p2, label="1:N", rad=0.0, color="#64748b"):
    ax.annotate(
        "", xy=p2, xytext=p1,
        arrowprops=dict(arrowstyle="->", color=color, lw=1.5,
                        connectionstyle=f"arc3,rad={rad}", shrinkA=3, shrinkB=3),
        zorder=5
    )
    mid_x = (p1[0] + p2[0]) / 2
    mid_y = (p1[1] + p2[1]) / 2 + 0.12
    ax.text(mid_x, mid_y, label, color=color, fontsize=7.5, fontweight='bold', ha='center', va='center', zorder=6)

# Relationships
# users -> orang_tua (1:1)
draw_arrow((4.2, 11.5), (5.2, 11.5), "1:1", color="#38bdf8")

# orang_tua -> siswa_orang_tua (1:N)
draw_arrow((8.4, 11.3), (9.4, 11.3), "1:N", color="#ec4899")

# siswa -> siswa_orang_tua (1:N)
draw_arrow((13.6, 11.3), (12.6, 11.3), "1:N", color="#ec4899")

# users -> siswa (1:1 optional)
draw_arrow((2.6, 13.2), (15.2, 13.2), "1:1 (opt)", rad=-0.25, color="#10b981")

# kelas -> siswa (1:N)
draw_arrow((18.0, 11.6), (16.8, 11.6), "1:N", color="#14b8a6")

# tahun_ajaran -> tarif_tagihan (1:N)
draw_arrow((18.0, 7.5), (16.8, 7.0), "1:N", color="#f59e0b")

# jenis_tagihan -> tarif_tagihan (1:N)
draw_arrow((18.0, 4.3), (16.8, 5.8), "1:N", color="#eab308")

# tarif_tagihan -> tagihan (1:N)
draw_arrow((15.2, 5.2), (15.2, 4.2), "1:N", color="#06b6d4")

# siswa -> tagihan (1:N)
draw_arrow((14.5, 9.8), (14.5, 4.2), "1:N", color="#10b981")

# tagihan -> detail_pembayaran (1:N)
draw_arrow((13.6, 2.4), (12.2, 2.4), "1:N", color="#f97316")

# pembayaran -> detail_pembayaran (1:N)
draw_arrow((7.8, 2.4), (8.8, 2.4), "1:N", color="#d946ef")

# users -> pembayaran (1:N)
draw_arrow((2.6, 10.2), (4.5, 4.4), "1:N", rad=0.2, color="#3b82f6")

# pembayaran -> notifikasi_gateway (1:N)
draw_arrow((4.2, 2.4), (3.4, 2.4), "1:N", color="#ef4444")

ax.set_xlim(0, 22)
ax.set_ylim(0, 15)
ax.axis('off')

plt.tight_layout()
output_path = r"C:\Users\ThinkPad i7\.gemini\antigravity\brain\d20a6afa-7219-4395-8388-f8d541c8dcd1\erd_export\erd_spp_online_baru.png"
plt.savefig(output_path, dpi=300, bbox_inches='tight', facecolor=fig.get_facecolor(), edgecolor='none')
print("Successfully generated ERD image:", output_path)

