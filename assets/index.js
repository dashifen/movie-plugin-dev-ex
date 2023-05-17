import metadata from './block.json';
import { registerBlockType } from '@wordpress/blocks';

registerBlockType(metadata, {
  edit: (props) => {
    return (
      "## Movie Block ##"
    )
  },
})