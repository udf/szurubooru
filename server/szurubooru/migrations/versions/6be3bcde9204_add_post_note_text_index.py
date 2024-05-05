"""
Add post note text index

Revision ID: 6be3bcde9204
Created at: 2024-05-05 16:50:49.949306
"""

import sqlalchemy as sa
from alembic import op

revision = "6be3bcde9204"
down_revision = "adcd63ff76a2"
branch_labels = None
depends_on = None


def upgrade():
    op.execute("CREATE EXTENSION IF NOT EXISTS pg_trgm;")
    op.create_index(
        "post_note_idx",
        "post_note",
        ["text"],
        unique=False,
        postgresql_ops={"text": "gin_trgm_ops"},
        postgresql_using="gin",
    )


def downgrade():
    op.drop_index(
        "post_note_idx",
        table_name="post_note",
        postgresql_ops={"text": "gin_trgm_ops"},
        postgresql_using="gin",
    )
